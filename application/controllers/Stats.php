<?php
defined('BASEPATH') OR exit('No direct script access allowed');

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;

class Stats extends MY_Controller {
    
    public function test() {
        echo 'OK';
    }

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

}
