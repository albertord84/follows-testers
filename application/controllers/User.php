<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class User extends MY_Controller {

	public function signin() {
    $this->load->library('logger');
		$username = $this->request_param('userName');
    $password = $this->request_param('password');
    try {
      $is_valid = $this->validate_creds($username, $password);
      if ($is_valid) {
        // guardar otras cosas de la sesion aqui...
        $this->session->token = $this->guid();
        $this->logger->write("The user $username logged in successfully", SYS_LOG);
        return $this->success('ok', [
          'token' => $this->session->token
        ]);
      }
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
    return $this->error('Unexpected error signing in. Contact system administrator.');
  }

  private function instagram_users($query) {
    $ch = curl_init("https://www.instagram.com/");
    $search_url = "https://www.instagram.com/web/search/topsearch/?context=blended&query=$query";
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, FALSE);
    curl_setopt($ch, CURLOPT_URL, $search_url);
    $html = curl_exec($ch);
    $content = json_decode($html);
    curl_close($ch);
    if (is_object($content) && $content->status === 'ok') {
      $users = $content->users;
      $mapped = array_map(function($user_data) {
        return $user_data->user;
      }, $users);
      return array_slice($mapped, 0, 10);
    }
    return false;
  }

  public function search($query) {
    try {
      $users = $this->instagram_users($query);
      if (!$users) {
        throw new Exception("Something happened fetching Instagram user list for $query");
      }
      return $this->success('ok', [
        'query' => $query,
        'users' => $users
      ]);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
  }

  private function check_emptyness($username, $password) {
    $empty_username = strcmp(trim($username), '') === 0;
    $empty_password = strcmp(trim($password), '') === 0;
    if ($empty_username || $empty_password) {
      return true;
    }
    return false;
  }

  private function check_exists($username, $source = USERS_SOURCE) {
    $json = file_get_contents($source);
    $list = json_decode($json, true);
    $filtered = array_filter($list, function($item) use ($username) {
      return $item['username'] === $username;
    });
    return count($filtered) === 1;
  }

  private function check_password($username, $password, $source = USERS_SOURCE) {
    $json = file_get_contents($source);
    $list = json_decode($json, true);
    $creds = "$username|$password";
    $filtered = array_filter($list, function($item) use ($creds) {
      $_creds = $item['username'] . '|' . $item['password'];
      return strcmp($_creds, $creds) === 0;
    });
    return count($filtered) === 1;
  }

  private function validate_creds($username, $password) {
    $some_empty = $this->check_emptyness($username, $password);
    if ($some_empty) {
      throw new \Exception('Username/password parameters must not be empty.');
    }
    $exists = $this->check_exists($username);
    if (!$exists) {
      throw new \Exception('Username/password not valid.');
    }
    $passwd_correct = $this->check_password($username, $password);
    if ($passwd_correct) {
      return true;
    }
    throw new \Exception('Not a valid username/password. Check your credentials again.');
  }

  public function follow() {
    try {
      $username = $this->request_param('userName');
      $password = $this->request_param('password');
      $userId = $this->request_param('userId');
      $instagram = new \InstagramAPI\Instagram();
      $instagram->login($username, $password, SIX_HOURS);
      $instagram->people->follow($userId);
      return $this->success();
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
  }

  public function following($query) {
    try {
      $username = $this->request_param('userName');
      $password = $this->request_param('password');
      $instagram = new \InstagramAPI\Instagram();
      $instagram->login($username, $password, SIX_HOURS);
      $rank = \InstagramAPI\Signatures::generateUUID();
      $followingResponse = $instagram->people->getSelfFollowing($rank, $query);
      return $this->success('ok', [
        'users' => $followingResponse->getUsers()
      ]);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
  }

  public function followers($query) {
    try {
      $username = $this->request_param('userName');
      $password = $this->request_param('password');
      $instagram = new \InstagramAPI\Instagram();
      $instagram->login($username, $password, SIX_HOURS);
      $rank = \InstagramAPI\Signatures::generateUUID();
      $followersResponse = $instagram->people->getSelfFollowers($rank, $query);
      return $this->success('ok', [
        'users' => $followersResponse->getUsers()
      ]);
    }
    catch(\Exception $ex) {
      return $this->error($ex->getMessage());
    }
  }

}
