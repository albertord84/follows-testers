<?php

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/common.php';

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

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

function login_instagram_by_proxy($data, $proxy) {
  $six_hours = 3590 * 6; // a bit less than 6h (3600 * 6)
  $instagram = null;
  try {
    $instagram = new \InstagramAPI\Instagram(true, false);
    $instagram->setProxy($proxy);
    $instagram->login($data->userName, $data->password, $six_hours);
    return $instagram;
  }
  catch(\Exception $loginEx) {
    printf("%s Could not log into Instagram: \"%s\"\n",
      time_str(), $loginEx->getMessage());
    exit(1);
  }
}

$test_users = parse_ini_file(ROOT_DIR . '/etc/test.users.ini', true);
$test_account = $test_users['yordanoweb'];

$data = [
  'userName' => $test_account['username'],
  'password' => $test_account['password'],
  "message" => "2dn msg...",
  "profileId" => "3990121320",
  "rankToken" => "4f3cf517-6ad4-4eb0-8c00-5297956b7095",
  "maxId" => null,
  "lastProf" => "",
  "finished" => false,
  "sent" => 0,
];

/*$ig = login_instagram_by_proxy(
  (object)$data,
  "tcp://johndoe:secret@172.84.73.213:21287"
);*/

$ig = login_instagram(
  (object)$data
);

