<?php

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/common.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

function is_web_request() {
  return is_cli() === false;
}

function create_pid_file() {
  try {
    file_put_contents(DIRECTS_PID_FILE, '');
  }
  catch(\Exception $pidEx) {
    printf("%s Could not create pid file: \"%s\"\n",
      time_str(), $pidEx->getMessage());
    exit(1);
  }
}

function remove_pid_file() {
  $pid = DIRECTS_PID_FILE;
  if (file_exists($pid)) {
    try {
      unlink($pid);
    }
    catch(\Exception $pidEx) {
      printf("%s Could not delete pid file: \"%s\"\n",
        time_str(), $pidEx->getMessage());
      exit(1);
    }
  }
}

function load_message($msg_filename) {
  try {
    if (!file_exists($msg_filename)) {
      throw new \Exception('Message file is not present');
    }
    $data = read_file($msg_filename);
    if ($data === false) {
      throw new \Exception('Unable to load message file');
    }
    return json_decode($data);
  }
  catch(\Exception $loadEx) {
    printf("%s Could not load message file: \"%s\"\n",
      time_str(), $loadEx->getMessage());
    exit(1);
  }
}

function next_prof_from($usersList, $lastProf) {
  $index = 0;
  $nextProf = array_reduce($usersList, function($carry, $user) use ($lastProf, $usersList, &$index) {
    if ($carry === null) {
      $next = array_key_exists($index + 1, $usersList) ?
        $usersList[$index + 1] : null;
      $carry = $user->getPk() === $lastProf ? $next : null;
    }
    $index++;
    return $carry;
  }, null);
  return $nextProf !== null ? $nextProf->getPk() : false;
}

function login_instagram($data) {
  $six_hours = 3590 * 6; // a bit less than 6h (3600 * 6)
  $instagram = null;
  try {
    $instagram = new \InstagramAPI\Instagram(true, false);
    $instagram->login($data->userName, $data->password, $six_hours);
    return $instagram;
  }
  catch(\Exception $loginEx) {
    printf("%s Could not log into Instagram: \"%s\"\n",
      time_str(), $loginEx->getMessage());
    exit(1);
  }
}

function get_next_recipient($instagram, $data) {
  try {
    $rankToken = $data->rankToken === null ?
      \InstagramAPI\Signatures::generateUUID() : $data->rankToken;
    $nextProf = null;
    $followersResponse = $instagram->people
      ->getFollowers($data->profileId, $rankToken, null,
                     $data->maxId === null ? null : $data->maxId);
    $list = $followersResponse->getUsers();
    $maxId = $followersResponse->getNextMaxId();
    if ($data->lastProf === '' || $data->lastProf === null) {
      $followers = $followersResponse->getUsers();
      if (count($followers) === 0) {
        throw new \Exception('Selected reference profile has no followers');
      }
      $firstProf = current($followers);
      $nextProf = $firstProf->getPk();
      return (object) [
        'lastProf' => $nextProf,
        'maxId' => $maxId,
        'rankToken' => $rankToken,
      ];
    }
    $nextProf = next_prof_from($list, $data->profileId);
    if (!$nextProf) {
      $maxId = $followersResponse->getNextMaxId();
      $followersResponse = $instagram->people->getFollowers($data->profileId,
        $rankToken, null, $maxId);
      $firstProf = current($followersResponse->getUsers());
      $nextProf = $firstProf->getPk();
    }
    return (object) [
      'lastProf' => $nextProf,
      'maxId' => $maxId,
      'rankToken' => $rankToken,
    ];
  }
  catch(\Exception $nextProfEx) {
    printf("%s Could not get the next recipient: \"%s\"\n",
      time_str(), $nextProfEx->getMessage());
    exit(1);
  }
}

function send($instagram, $prof, $msg) {
  try {
    $instagram->direct->sendText([ 'users' => [ $prof ] ], $msg);
    return true;
  }
  catch(\Exception $sendEx) {
    printf("%s Could not send the message: \"%s\"\n",
      time_str(), $sendEx->getMessage());
    exit(1);
  }
}

function set_stats($fileName, $data) {
  try {
    $fileObj = json_decode(read_file($fileName));
    $fileObj->sent = (int)$fileObj->sent + 1;
    $fileObj->lastProf = $data->lastProf;
    $fileObj->maxId = $data->maxId;
    $fileObj->rankToken = $data->rankToken;
    $json = json_encode($fileObj, JSON_PRETTY_PRINT);
    write_file($fileName, $json);
  }
  catch(\Exception $statEx) {
    printf("%s Could not update stats: \"%s\"\n",
      time_str(), $statEx->getMessage());
    exit(1);
  }
}

$test_users = parse_ini_file(ROOT_DIR . '/etc/test.users.ini', true);
$test_account = $test_users['yordanoweb'];

$msg_filename = '/home/yordano/Projects/dumbu-tester/var/yordanoweb.1.json';

$data = load_message($msg_filename);
$ig = login_instagram($data);
$recipData = get_next_recipient($ig, $data);

$sent = send($ig, $recipData->lastProf, $data->message);

$data->rankToken = $recipData->rankToken;
$data->maxId = $recipData->maxId;
$data->lastProf = $recipData->lastProf;
set_stats($msg_filename, $data);

echo $sent ? 'success' : 'failed';
echo PHP_EOL;
