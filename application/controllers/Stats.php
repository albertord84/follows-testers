<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stats extends MY_Controller {
    
    public function server(string $server_abrev) {
        try {
            $this->load->library('logs');
            $stats = $this->logs->from_server($server_abrev);
            return $this->success('ok', [
                'stats' => $stats
            ]);
        }
        catch(\Exception $serverStatsEx) {
            return $this->error('Server stats error: ' . $serverStatsEx->getMessage());
        }
    }

    public function users(string $server, string $log_date, int $page = 1) {
        try {
            $this->load->library('logs');
            $stats = $this->logs->users_from($server, $log_date, $page);
            return $this->success('ok', [
                'data' => $stats
            ]);
        }
        catch(\Exception $serverStatsEx) {
            return $this->error('Users stats from log date error: ' . $serverStatsEx->getMessage());
        }
    }

}
