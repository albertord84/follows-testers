<?php

//////////////////////////////////////////////////////////////////////////
// ESTO ES LO QUE HAY QUE CAMBIAR SEGUN EL HOST DONDE ESTE ESTE SCRIPT
//////////////////////////////////////////////////////////////////////////
$logs_dir = '/home/yordano/Projects/';

// Variables y parametros requeridos por este script

$rnd = mt_rand(0, 10) * mt_rand(10, 100) * mt_rand(100, 1000);
$log_date = $_REQUEST['log'];
$page = isset($_REQUEST['p']) ? ($_REQUEST['p'] == '0' ? 1 : $_REQUEST['p']) : 1;
$client = isset($_REQUEST['c']) ? $_REQUEST['c'] : false;
$log_file = sprintf("/tmp/dumbo-worker.%s.log", $rnd);
$tmp_log = sprintf("/tmp/dumbo-worker.%s.tmp", $rnd);

function clean_temps() {
    $arg_list = func_get_args();
    foreach ($arg_list as $file) {
        unlink($file);
    }
}

// Juntar en un solo archivo las trazas de todos los worker

shell_exec(
    sprintf(
        "cat %s/dumbo-worker*%s.log > %s",
        $logs_dir, $log_date, $log_file
    )
);

// Abrir el archivo creado y otro para toda la basura generada
$handle = fopen($log_file, "r");
$tmp_handle = fopen($tmp_log, "w");

if ($handle) {
    $stat = null;
    $c = 0;
    while (( $line = fgets($handle) )!== false) {
        // Cuando aparece la linea Client se crea una nueva entrada
        // y se emite hacia el archivo temporal la entrada anterior
        if (preg_match('/^Client\: (\d+)/', $line, $client_data)===1) {
            if ($stat !== null) {
                $data = sprintf("%s", json_encode($stat));
                // shell_exec("echo '$data' >> $tmp_log");
                fputs($tmp_handle, $data . PHP_EOL);
                $c++;
            }
            $stat = []; // comienzo uno nuevo
            $stat['client_id'] = (int) $client_data[1];
            $stat['num'] = $c;
        }
        // Cantidad de follows alcanzados para el cliente
        if (preg_match('/like firsts = \d+\): (\d+) \</', $line, $follows)===1) {
            $stat['followed'] = (int) $follows[1];
        }
        // Perfil de referencia
        if (strstr($line, 'nRef Profil: ')!==false) {
            preg_match('/nRef Profil: (.*)\</', $line, $prof_data);
            $stat['ref_prof'] = $prof_data[1];
        }
        // Fecha y hora
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

// Si se paso el parametro 'client' = $_REQUEST['c'] es porque solo
// se quieren las estadisticas para ese solo cliente
if ($client) {
    $client_times = trim(shell_exec("grep -c $client $tmp_log"));
    if ((int) $client_times == 0) {
        echo json_encode(['stats' => [] ]);
        clean_temps($log_file, $tmp_log);
        die();
    }
    $lines = trim(shell_exec("grep $client $tmp_log"));
    $array = explode(PHP_EOL, $lines);
    $objects = array_map(function ($str) {
        return (array)json_decode($str);
    }, $array);
    echo json_encode(['stats' => $objects]);
    clean_temps($log_file, $tmp_log);
    die();
}

// Si no se paso el parametro client, es porque se estan mostrando
// todas las estadisticas recolectadas
$total = trim(shell_exec("grep -c client_id $tmp_log"));

// Si es la pagina inicial o la pagina 0
if ($page == 1 || $page == 0) {
    $head = trim(shell_exec("head -n 50 $tmp_log"));
    $array = explode(PHP_EOL, $head);
    $objects = array_map(function($str) {
        return (array)json_decode($str);
    }, $array);
    echo json_encode(['total' => $total, 'page' => $page, 'stats' => $objects]);
}
else { // Si se esta paginado
    $top = 50 * $page;
    $head = trim(shell_exec("head -n $top $tmp_log | tail -n 50"));
    $array = explode(PHP_EOL, $head);
    $objects = array_map(function($str) {
        return (array)json_decode($str);
    }, $array);
    echo json_encode(['total' => $total, 'page' => $page, 'stats' => $objects]);
}

clean_temps($log_file, $tmp_log);