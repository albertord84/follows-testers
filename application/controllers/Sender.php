<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class Sender extends MY_Controller {

  /**
   * Invoked from the cron using a PHP executable sentence, passing
   * the index.php filepath as first parameter, the name of this
   * class in lower case as second parameter, this method name as
   * third parameter, and the message filename to process as fourth
   * parameter.
   * 
   * @param $msgFile Message filename to process.
   */
  public function start($msgFile) {
    set_time_limit(0);

    try {
      if ($this->is_web_request()) {
        printf("%s Not allowed to run from the browser. Terminating right now.\n",
          $this->time_str());
        die();
      }
  
      if ($this->is_running()) {
        printf("%s We are already running. I will terminate right now.\n",
          $this->time_str());
        die();
      }
  
      $this->create_pid_file();
      $data = $this->load_message($msgFile);
  
      if ($data->finished) {
        printf("%s This message is no longer being processed. Terminating...\n",
          $this->time_str());
        die();
      }
  
      $instagram = $this->login_instagram($data);
      $recipData = $this->get_next_recipient($instagram, $data);
      $sent = $this->send($instagram, $recipData->lastProf, $data->message);
  
      if (!$sent) {
        throw new \Exception('Unable to text the recipient account ' . $recipData->lastProf);
      }

      $data->maxId = $recipData->maxId;
      $data->lastProf = $recipData->lastProf;
      $data->rankToken = $recipData->rankToken;
      $this->set_stats($msgFile, $data);
  
      $this->remove_pid_file();
    }
    catch(\Exception $mainEx) {
      echo $mainEx->getMessage();
    }
  }

  private function login_instagram($data) {
    $six_hours = 3590 * 6; // a bit less than 6h (3600 * 6)
    $instagram = null;
    try {
      $instagram = new \InstagramAPI\Instagram();
      $instagram->login($data->userName, $data->password, $six_hours);
      return $instagram;
    }
    catch(\Exception $loginEx) {
      printf("%s Could not log into Instagram: \"%s\"\n",
        $this->time_str(), $loginEx->getMessage());
      exit(1);
    }
  }

  private function get_next_recipient($instagram, $data) {
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
        $this->time_str(), $nextProfEx->getMessage());
      exit(1);
    }
  }

  private function send($instagram, $prof, $msg) {
    try {
      $instagram->direct->sendText([ 'users' => [ $prof ] ], $msg);
      return true;
    }
    catch(\Exception $sendEx) {
      printf("%s Could not send the message: \"%s\"\n",
        $this->time_str(), $sendEx->getMessage());
      exit(1);
    }
  }

  private function next_prof_from($usersList, $lastProf) {
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

  private function set_stats($fileName, $data) {
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
        $this->time_str(), $statEx->getMessage());
      exit(1);
    }
  }

  private function is_web_request() {
    return is_cli() === false;
  }

  /**
   * Returns date and time in log format: MMM DD HH:mm:ss
   */
  private function time_str() {
    $d = date('j');
    return sprintf("%s %s %s", date('M'),
      strlen($d) === 2 ? $d : ' ' . $d,
      date('G:i:s'));
  }

  private function is_running() {
    return file_exists(DIRECTS_PID_FILE);
  }

  private function create_pid_file() {
    try {
      file_put_contents(DIRECTS_PID_FILE, '');
    }
    catch(\Exception $pidEx) {
      printf("%s Could not create pid file: \"%s\"\n",
        $this->time_str(), $pidEx->getMessage());
      exit(1);
    }
  }

  private function remove_pid_file() {
    $pid = DIRECTS_PID_FILE;
    if (file_exists($pid)) {
      try {
        unlink($pid);
      }
      catch(\Exception $pidEx) {
        printf("%s Could not delete pid file: \"%s\"\n",
          $this->time_str(), $pidEx->getMessage());
        exit(1);
      }
    }
  }

  private function load_message($msg_filename) {
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
        $this->time_str(), $loadEx->getMessage());
      exit(1);
    }
  }

}
