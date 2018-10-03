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
    $this->load->library('logger');
    set_time_limit(0);
    
    try {
      if ($this->is_web_request()) {
        $this->logger->write("ERROR: Not allowed to run from the browser. Terminating right now.\n",
			SENDER_LOG);
        die();
      }
  
      if ($this->is_running()) {
        $this->logger->write("ERROR: We are already running. I will terminate right now.\n",
			SENDER_LOG);
        die();
      }
  
      $data = $this->load_message(DIRECTS_POOL_DIR . "/$msgFile");
  
      if ($data->finished) {
        $this->logger->write("INFO: This message is no longer being processed. Terminating...\n",
			SENDER_LOG);
        die();
      }
  
      $this->create_pid_file();
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
      $this->logger->write(
        sprintf(
          "INFO: The sender %s successfully texted to %s",
          $data->profileId,
          $data->lastProf
        ),
	  	SENDER_LOG
      );
    }
    catch(\Exception $mainEx) {
      // set message as finished to stop processing
      $this->set_message_finished($msgFile);
      // register and shout out the exception
      echo $mainEx->getMessage() . PHP_EOL;
      $this->logger->write("ERROR: " . $mainEx->getMessage(),
		  SENDER_LOG);
      // remove process id
      $this->remove_pid_file();
    }
  }

  private function set_message_finished($fileName) {
    $fileObj = json_decode(read_file(DIRECTS_POOL_DIR . "/$fileName"));
    $fileObj->finished = true;
    $json = json_encode($fileObj, JSON_PRETTY_PRINT);
    write_file(DIRECTS_POOL_DIR . "/$fileName", $json);
  }


  private function login_instagram($data) {
    $instagram = null;
    $instagram = new \InstagramAPI\Instagram(false, true);
    $instagram->login($data->userName, $data->password, SIX_HOURS);
    return $instagram;
  }

  private function get_next_recipient($instagram, $data) {
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
    $nextProf = $this->next_prof_from($list, $data->profileId);
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

  private function send($instagram, $prof, $msg) {
    $instagram->direct->sendText([ 'users' => [ $prof ] ], $msg);
    return true;
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
    $fileObj = json_decode(read_file(DIRECTS_POOL_DIR . "/$fileName"));
    $fileObj->sent = (int)$fileObj->sent + 1;
    $fileObj->lastProf = $data->lastProf;
    $fileObj->maxId = $data->maxId;
    $fileObj->rankToken = $data->rankToken;
    $json = json_encode($fileObj, JSON_PRETTY_PRINT);
    write_file(DIRECTS_POOL_DIR . "/$fileName", $json);
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
    file_put_contents(DIRECTS_PID_FILE, '');
  }

  private function remove_pid_file() {
    $pid = DIRECTS_PID_FILE;
    if (file_exists($pid)) {
      unlink($pid);
    }
  }

  private function load_message($msg_filename) {
    $data = read_file($msg_filename);
    if ($data === false) {
      throw new \Exception('Unable to load message file');
    }
    return json_decode($data);
  }

  public function delivery() {
    try {
      $this->load->library('logger');
      $log = $this->logger->delivery_log();
      return $this->success('ok', [
        'log' => $log
      ]);
    }
    catch(\Exception $deliveryEx) {
      return $this->error('Delivery log error: ' . $deliveryEx->getMessage());
    }
  }

  public function messages($username) {
    try {
      $user_message_filenames = $this->only_user_msg_files($username);
      $messages = $this->prepare_message_list(
        $user_message_filenames,
        'msg_filenames_to_objects',
        'active_messages_only',
        'add_reference_prof_data',
        'remove_user_creds'
      );
      return $this->success('ok', [ 'messages' => $messages ]);
    }
    catch(\Exception $msgListEx) {
      return $this->error("Unable to list $username messages: " . $msgListEx->getMessage());
    }
  }

  private function msg_filenames_to_objects($msg_filenames) {
    $message_objects = array_map(function($msg_filename) {
      $data = read_file(DIRECTS_POOL_DIR . '/' . $msg_filename);
      $msg = json_decode($data);
      return $msg;
    }, $msg_filenames);
    return $message_objects;
  }

  private function add_reference_prof_data($messages) {
    $instagram = null;
    return array_map(function($msg) use ($instagram) {
      if ($instagram === null) {
        $instagram = new \InstagramAPI\Instagram(false, true);
        $instagram->login($msg->userName, $msg->password, SIX_HOURS);
      }
      $user = $instagram->people
        ->getInfoById($msg->profileId)
        ->getUser();
      $profName = $user->getUsername();
      $pic = $user->getProfilePicUrl();
      $msg->profName = $profName;
      $msg->profPic = $pic;
      return $msg;
    }, $messages);
  }

  private function remove_user_creds($user_messages) {
    $messages = array_map(function($msg) {
      $array = (array) $msg;
      unset($array['userName']);
      unset($array['password']);
      return (object) $array;
    }, $user_messages);
    return $messages;
  }

  private function only_user_msg_files($username) {
    $map = directory_map(DIRECTS_POOL_DIR, 1);
    $user_messages = array_filter($map, function($msg_file) use ($username) {
      return strstr($msg_file, $username) !== false;
    });
    return $user_messages;
  }

  private function active_messages_only($messages) {
    return array_filter($messages, function($msg) {
      return $msg->finished !== true;
    });
  }

  private function prepare_message_list($messages, ...$funcs) {
    $methods = array_slice(func_get_args(), 1);
    $messages = array_reduce($methods, function($carry, $method) {
      $carry = $this->$method($carry);
      return $carry;
    }, $messages);
    return $messages;
  }

}
