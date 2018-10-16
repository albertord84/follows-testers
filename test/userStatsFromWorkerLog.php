<?php

//////////////////////////////////////////////////////////////////////////
// ESTO ES LO QUE HAY QUE CAMBIAR SEGUN EL HOST DONDE ESTE ESTE SCRIPT
//////////////////////////////////////////////////////////////////////////
$logs_dir = '/opt/lampp/htdocs/dumbu/worker/log';

$rnd = mt_rand(0, 10) * mt_rand(10, 100) * mt_rand(100, 1000);
$log_date = $_REQUEST['log'];
$out_log = sprintf("/tmp/dumbo-worker.%s.log", $rnd);

shell_exec(
    sprintf(
        "cat %s/dumbo-worker*%s.log > %s",
        $logs_dir, $log_date, $out_log
    )
);

define(
    'LOG_FILE',
    $out_log
);

$handle = fopen(LOG_FILE, "r");

if ($handle) {
    $stat = null;
    $c = 0;
    printf("[");
    while (($line = fgets($handle)) !== false) {
        if (preg_match('/^Client\: (\d+)/', $line, $client_data)===1) {
            if ($stat !== null) {
                printf("%s", $c === 0 ? "" : ",");
                printf("%s", json_encode($stat));
                $c++;
            }
            $stat = []; // comienzo uno nuevo
            $stat['client_id'] = (int) $client_data[1];
        }
        if (preg_match('/like firsts = \d+\): (\d+) \</', $line, $follows)===1) {
            $stat['followed'] = (int) $follows[1];
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
    printf("]");
} else {
    echo 'Error al abrir archivo ' . LOG_FILE;
}

unlink($out_log);
