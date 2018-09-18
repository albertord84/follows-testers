<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

  protected function request_param($param_name) {
    $input = json_decode(file_get_contents('php://input'), true);
    return $input[$param_name];
  }

  protected function input_data() {
    $input = json_decode(file_get_contents('php://input'));
    return $input;
  }

  protected function error($msg = 'Error', $data = []) {
    $merged = array_merge(
      [ 'error' => $msg ],
      $data
    );
    $output = json_encode($merged,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $this->output
      ->set_status_header(500, $msg)
      ->set_content_type('application/json', 'utf-8')
      ->set_output($output);
  }

  protected function success($msg = 'ok', $data = []) {
    $merged = array_merge(
      [ 'status' => $msg ],
      $data
    );
    $output = json_encode($merged,
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return $this->output
      ->set_status_header(200)
      ->set_content_type('application/json', 'utf-8')
      ->set_output($output);
  }

  protected function guid() {
    $g = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
      mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535),
      mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535),
      mt_rand(0, 65535), mt_rand(0, 65535));
    return strtolower($g);
  }

}
