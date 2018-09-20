<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron {

  public function is_time($interval) {
    $hour = (int) date('H');
    $mins = (int) date('i');
    if ($mins !== 0) { return false; }
    return $hour % (int) $interval === 0;
  }

}