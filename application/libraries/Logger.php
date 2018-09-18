<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logger {

  public function write($dest_logfile, $msg = '') {
    $datetime = $this->time_str();
    $data = sprintf("%s - %s", $datetime, $msg . PHP_EOL);
    file_put_contents($dest_logfile, $data, FILE_APPEND);
  }

  public function add_error_header($info_msg) {
    $is_info = strstr($info_msg, 'INFO') !== false;
    $transformed = $is_info ? $info_msg : 'ERROR: ' . $info_msg;
    return $transformed;
  }

  public function last($login_log = LOGIN_TEST_LOG, $lines = 20) {
    $result = shell_exec("tail -n $lines $login_log");
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

}