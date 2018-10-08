<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron {
    
    public function is_time($interval) {
        $hour = (int) date('H');
        $mins = (int) date('i');
        if ($mins !== 0) { return false; }
        return $hour % (int) $interval === 0;
    }
    
    /**
    * Returns date and time in log format: MMM DD HH:mm:ss
    */
    public function time_str() {
        $d = date('j');
        return sprintf("%s %s %s", date('M'),
        strlen($d) === 2 ? $d : ' ' . $d,
        date('G:i:s'));
    }
    
}