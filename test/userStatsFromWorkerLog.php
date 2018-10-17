<?php

//////////////////////////////////////////////////////////////////////////
// ESTO ES LO QUE HAY QUE CAMBIAR SEGUN EL HOST DONDE ESTE ESTE SCRIPT
//////////////////////////////////////////////////////////////////////////
$logs_dir = '/opt/lampp/htdocs/dumbu/worker/log';

$rnd = mt_rand(0, 10) * mt_rand(10, 100) * mt_rand(100, 1000);
$log_date = $_REQUEST['log'];
$page = isset($_REQUEST['p']) ? ($_REQUEST['p'] == '0' ? 1 : $_REQUEST['p']) : 1;
$out_log = sprintf("/tmp/dumbo-worker.%s.log", $rnd);
$tmp_log = sprintf("/tmp/dumbo-worker.%s.tmp", $rnd);

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
$tmp_handle = fopen($tmp_log, "w");

if ($handle) {
    $stat = null;
    $c = 0;
    while (( $line = fgets($handle) )!== false) {
        if (preg_match('/^Client\: (\d+)/', $line, $client_data)===1) {
            if ($stat !== null) {
                $data = sprintf("%s", json_encode($stat));
                // shell_exec("echo '$data' >> $tmp_log");
                fputs($tmp_handle, $data . PHP_EOL);
                $c++;
            }
            $stat = []; // comienzo uno nuevo
            $stat['client_id'] = (int) $client_data[1];
            $stat['num'] = (int) $c * $page;
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
    fclose($tmp_handle);
} else {
    echo 'Error al abrir archivo ' . LOG_FILE;
}

if ($page == 1 || $page == 0) {
    $head = trim(shell_exec("head -n 50 $tmp_log"));
    $array = explode(PHP_EOL, $head);
    $objects = array_map(function($str) {
        return (array)json_decode($str);
    }, $array);
    echo json_encode($objects);
}
else {
    $top = 50 * $page;
    $head = trim(shell_exec("head -n $top $tmp_log | tail -n 50"));
    $array = explode(PHP_EOL, $head);
    $objects = array_map(function($str) {
        return (array)json_decode($str);
    }, $array);
    echo json_encode($objects);
}

unlink($out_log);
unlink($tmp_log);
