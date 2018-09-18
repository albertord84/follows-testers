<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class Texting extends MY_Controller {

  public function register() {
    $max_msg_length = 299;
    try {
      $data = [
        'userName' => $this->request_param('userName'),
        'password' => $this->request_param('password'),
        'message' => substr($this->request_param('message'), 0, $max_msg_length),
        'profileId' => $this->request_param('profileId'),
        'rankToken' => null,
        'maxId' => null,
        'lastProf' => '',
        'finished' => false,
        'sent' => 0
      ];
      $this->save_message($data);
      return $this->success();
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
  }

  private function save_message($data) {
    $max_directs = 3;
    $timestamp = date('U');
    $json = json_encode($data, JSON_PRETTY_PRINT);
    $userName = $data['userName'];
    try {
      $count = $this->msg_count($userName);
      if ($count >= $max_directs) {
        throw new \Exception("The user account $userName has reached the maximum $count of messages permitted.");
      }
      $last_num = $this->last_msg_num($userName);
      $filename = sprintf("%s.%s.json", $userName, ++$last_num);
      write_file(DIRECTS_POOL_DIR . '/' . $filename, $json);
    }
    catch(\Exception $writeEx) {
      throw new \Exception("The new message was not registered. CAUSE: " .
        $writeEx->getMessage());
    }
  }

  /**
   * As every user is only allowed to start three campaigns,
   * we iterate over the var directory where messages are stored.
   * Then we count all the filenames containing the 'userName'.
   */
  private function msg_count($userName, $count = 3) {
    $filtered = [];
    try {
      $files = directory_map(DIRECTS_POOL_DIR, 1);
      $filtered = array_filter($files, function($file) use ($userName) {
        $belongs_to_user = strstr($file, $userName) !== false;
        if ($belongs_to_user) {
          $data = json_decode(read_file(DIRECTS_POOL_DIR . "/$file"));
          return $data->finished === false;
        }
        return false;
      });
    }
    catch(\Exception $fileEx) {
      throw new \Exception("Unable to obtain user message count. CAUSE: " .
        $fileEx->getMessage());
    }
    return count($filtered);
  }

  /**
   * Calculates the next msg number to store from the previously
   * stored message files.
   */
  private function last_msg_num($userName) {
    $last_num = 0;
    try {
      $files = directory_map(DIRECTS_POOL_DIR, 1);
      $filtered = array_filter($files, function($file) use ($userName) {
        $belongs_to_user = strstr($file, $userName) !== false;
        if ($belongs_to_user) {
          $data = json_decode(read_file(DIRECTS_POOL_DIR . "/$file"));
          return $data->finished === false;
        }
        return false;
      });
      $last_msg_file = end($filtered);
      $pattern = "/($userName)|(\.)|(json)/";
      $last_num = preg_replace($pattern, '', $last_msg_file);
    }
    catch(\Exception $fileEx) {
      throw new \Exception("Unable to obtain next message number. CAUSE: " .
        $fileEx->getMessage());
    }
    return $last_num;
  }

}
