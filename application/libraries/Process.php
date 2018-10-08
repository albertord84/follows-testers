<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process {
    
    public function is_web_request() {
        return is_cli() === false;
    }
    
    public function is_running() {
        return file_exists(DIRECTS_PID_FILE);
    }
    
    public function create_pid_file() {
        file_put_contents(DIRECTS_PID_FILE, '');
    }
    
    public function remove_pid_file() {
        $pid = DIRECTS_PID_FILE;
        if (file_exists($pid)) {
            unlink($pid);
        }
    }
    
}