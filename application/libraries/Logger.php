<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger {

  public function write($msg, $log_file) {
    $datetime = $this->time_str();
    $data = sprintf("%s - %s", $datetime, $msg . PHP_EOL);
    file_put_contents($log_file, $data, FILE_APPEND);
  }

  public function add_error_header($info_msg) {
    $is_info = strstr($info_msg, 'INFO') !== false;
    $transformed = $is_info ? $info_msg : 'ERROR: ' . $info_msg;
    return $transformed;
  }

  public function last($lines = 20) {
    $result = shell_exec("tail -n $lines " . LOGIN_TEST_LOG);
    $cmd_out_array = explode("\n", $result);
    return $cmd_out_array;
  }

  private function time_str() {
    $d = date('j');
    $month = date('M');
    $day = strlen($d) === 2 ? $d : ' ' . $d;
    $hour_min_secs = date('G:i:s');
    return sprintf("%s %s %s", $month, $day, $hour_min_secs);
  }

  public function delivery_log($lines = 20) {
    if (file_exists(SENDER_LOG)) {
      $lines = shell_exec("tail -n $lines " . SENDER_LOG);
      return preg_split("/\n|\r/", $lines, -1, PREG_SPLIT_NO_EMPTY);
    }
    return [];
  }

}
