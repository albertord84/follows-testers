<?php

define('LOG_FILE', $argv[1]);

$handle = fopen(LOG_FILE, "r");

if ($handle) {
    $stat = null;
    while (($line = fgets($handle)) !== false) {
        if (preg_match('/^Client\: (\d+)/', $line, $client_data)===1) {
            print_r($stat); // imprimo el anterior
            $stat = []; // comienzo uno nuevo
            $stat['client_id'] = $client_data[1];
        }
        if (preg_match('/like firsts = \d+\): (\d+) \</', $line, $follows)===1) {
            $stat['followed'] = $follows[1];
        }
        if (strstr($line, 'nRef Profil: ')!==false) {
            preg_match('/nRef Profil: (.*)\</', $line, $prof_data);
            $stat['ref_prof'] = $prof_data[1];
        }
        if (preg_match('/Count: \d+ Hasnext: \d+ - (.*)\</', $line, $time_data)===1) {
            list($date, $time) = explode(' ', $time_data[1]);
            $stat['date'] = $date;
            $stat['time'] = $time;
        }
    }
    fclose($handle);
} else {
    echo 'Error al abrir archivo ' . LOG_FILE;
}
