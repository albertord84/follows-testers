<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages {
    
    public function set_message_finished($fileName) {
        $fileObj = json_decode(read_file(DIRECTS_POOL_DIR . "/$fileName"));
        $fileObj->finished = true;
        $json = json_encode($fileObj, JSON_PRETTY_PRINT);
        write_file(DIRECTS_POOL_DIR . "/$fileName", $json);
    }
    
    public function load_message($msg_filename) {
        $data = read_file($msg_filename);
        if ($data === false) {
            throw new \Exception('Unable to load message file');
        }
        return json_decode($data);
    }
    
    public function msg_filenames_to_objects($msg_filenames) {
        $message_objects = array_map(function($msg_filename) {
            $data = read_file(DIRECTS_POOL_DIR . '/' . $msg_filename);
            $msg = json_decode($data);
            return $msg;
        }, $msg_filenames);
        return $message_objects;
    }
    
    public function add_reference_prof_data($messages) {
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
    
    public function remove_user_creds($user_messages) {
        $messages = array_map(function($msg) {
            $array = (array) $msg;
            unset($array['userName']);
            unset($array['password']);
            return (object) $array;
        }, $user_messages);
        return $messages;
    }
    
    public function only_user_msg_files($username) {
        $map = directory_map(DIRECTS_POOL_DIR, 1);
        $user_messages = array_filter($map, function($msg_file) use ($username) {
            return strstr($msg_file, $username) !== false;
        });
        return $user_messages;
    }
    
    public function active_messages_only($messages) {
        return array_filter($messages, function($msg) {
            return $msg->finished !== true;
        });
    }
    
    public function prepare_message_list($messages, ...$funcs) {
        $methods = array_slice(func_get_args(), 1);
        $messages = array_reduce($methods, function($carry, $method) {
            $carry = $this->$method($carry);
            return $carry;
        }, $messages);
        return $messages;
    }

    public function update($fileName, $data) {
        $fileObj = json_decode(read_file(DIRECTS_POOL_DIR . "/$fileName"));
        $fileObj->sent = (int)$fileObj->sent + 1;
        $fileObj->lastProf = $data->lastProf;
        $fileObj->maxId = $data->maxId;
        $fileObj->rankToken = $data->rankToken;
        $json = json_encode($fileObj, JSON_PRETTY_PRINT);
        write_file(DIRECTS_POOL_DIR . "/$fileName", $json);
    }
    
}