<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

  public function save() {
    $json = file_get_contents("php://input");
    $data = json_decode($json);
    try {
      write_file(ROOT_DIR . '/etc/login.test.json', $json);
      return $this->success('ok', [
        'userName' => $data->userName,
        'password' => $data->password,
        'interval' => $data->interval,
        'activated' => $data->activated
      ]);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
    return $this->error('Unexpected error saving login test data. Contact system administrator.');
  }

  public function load() {
    $json = file_get_contents(ROOT_DIR . '/etc/login.test.json');
    $array_data = json_decode($json, true);
    try {
      return $this->success('ok', $array_data);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
    return $this->error('Unexpected error loading login test data. Contact system administrator.');
  }

  public function cron($exec_inmediate = 'false') {
    $output = null;
    $this->load->library('cron');
    $this->load->library('logger');
    $data = $this->get_login_test_data();
    $is_time = $this->cron->is_time($data->interval);
    $is_active = strcmp($data->activated, 'on') === 0;
    // \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
    // $instagram = new \InstagramAPI\Instagram();
    try {
      if ($exec_inmediate === 'false') {
        $this->stop_if_not_ready($is_time, $is_active);
      }
      $five_hours = 3600 * 5;
      $proxy = PROXIES[0];
      /* $instagram->setProxy(
        sprintf(
          "tcp://%s:%s@%s:%s",
          $proxy['user'],
          $proxy['pass'],
          $proxy['ip'],
          $proxy['port']
        )
      );
      $instagram->login($data->userName, $data->password, $five_hours); */
      $output = shell_exec(
        sprintf(
          "/opt/lampp/bin/php %s/test/browserLogin.php %s %s",
          __DIR__ . '/../../',
          $data->userName,
          $data->password
        )
      );
      /*if (preg_match('/error|Error|ERROR/', $output)===1) {
        throw new \Exception(preg_replace('/\n/', ' ', $output));
      }*/
      if (preg_match('/authenticated\"\: false/', $output)===1) {
        $this->logger->error($output, LOGIN_TEST_LOG);
        throw new \Exception(
          sprintf("Login test for user %s failed", $data->userName)
        );
      }
      $success_msg = sprintf("Login test with user %s completed successfully.",
        $data->userName);
      $this->logger->write($success_msg, LOGIN_TEST_LOG);
      $this->logger->write($output, LOGIN_TEST_LOG);
    }
    catch(\Exception $e) {
      $this->logger->error($e->getMessage(), LOGIN_TEST_LOG);
    }
  }

  public function log() {
    $this->load->library('logger');
    try {
      $last_lines = $this->logger->last();
      return $this->success('ok', [ 'log' => $last_lines ]);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
    return $this->error('Unexpected error getting login test log. Contact system administrator.');
  }

  private function stop_if_not_ready($is_time, $is_active) {
    if ($is_time === false || $is_active === false) {
      $stop_msg = 'INFO: Test execution hour have not arriven yet or the account is inactive.';
      $this->logger->write($stop_msg, LOGIN_TEST_LOG);
      exit(0);
    }
  }

  private function get_login_test_data($source = LOGIN_TEST_DATA) {
    $json = file_get_contents($source);
    return json_decode($json);
  }

}
