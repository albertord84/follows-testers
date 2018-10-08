<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class Sender extends MY_Controller {

  /**
   * Invocar desde el cron pasando el camino a este script PHP
   * como primer parametro; el nombre de esta clase en minuscula
   * como segundo parametro; este metodo como tercer parametro;
   * y como cuarto parametro el nombre del archivo JSON que es
   * el mensaje que se procesara, y que se encuentra dentro del
   * directorio var.
   * 
   * @param $msgFile Nombre del archivo del mensaje a procesar.
   */
  public function start($msgFile) {
    $this->load->library('logger');
    $this->load->library('process');
    $this->load->library('instagram');
    set_time_limit(0);
    
    try {
      if ($this->process->is_web_request()) {
        $this->logger->write("ERROR: Not allowed to run from the browser. Terminating right now.\n",
			    SENDER_LOG);
        die();
      }
  
      if ($this->process->is_running()) {
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
  
      $this->process->create_pid_file();
      $instagram = $this->instagram->login_instagram($data);
      $recipData = $this->instagram->get_next_recipient($instagram, $data);
      $sent = $this->instagram->send($instagram, $recipData->lastProf, $data->message);
  
      if (!$sent) {
        throw new \Exception('Unable to text the recipient account ' . $recipData->lastProf);
      }

      $data->maxId = $recipData->maxId;
      $data->lastProf = $recipData->lastProf;
      $data->rankToken = $recipData->rankToken;
      $this->set_stats($msgFile, $data);

      $this->process->remove_pid_file();
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
      $this->process->remove_pid_file();
    }
  }

  private function set_message_finished($fileName) {
    $fileObj = json_decode(read_file(DIRECTS_POOL_DIR . "/$fileName"));
    $fileObj->finished = true;
    $json = json_encode($fileObj, JSON_PRETTY_PRINT);
    write_file(DIRECTS_POOL_DIR . "/$fileName", $json);
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
