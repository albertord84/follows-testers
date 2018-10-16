<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logs {

    /**
     * @param string server_abrev Abreviatura del nombre del servidor. Esta debe estar
     * declarada en el archivo de configuracion stat.servers.json dentro del directorio
     * etc en la raiz de la aplicacion.
     * 
     * @return array
     */
    public function from_server(string $server_abrev) {
        $statServersConfig = $this->load_config();
        $server = $this->get_server($server_abrev, $statServersConfig->servers);
        if ($server === null) {
            throw new \Exception('No stats found for server ' . $server_abrev);
        }
        $host = $server->server;
        $url = sprintf("https://%s/%s", $host, $statServersConfig->script);
        $remote_content = file_get_contents($url);
        $log_dates_array = (array) json_decode($remote_content);
        return $log_dates_array;
    }

    private function load_config() {
        $statServersJsonConfig = file_get_contents(APPPATH . '/../etc/stat.servers.json');
        $statServersConfig = json_decode($statServersJsonConfig);
        return $statServersConfig;
    }

    private function get_server(string $abrev, array $servers) {
        $filtered_list = array_filter(
            $servers,
            function($server) use ($abrev) {
                return $server->abrev === $abrev;
            }
        );
        return count($filtered_list) === 0 ? null : current($filtered_list);
    }

}
