<?php

/////////////////////////////////////////////////////////////////////////////////
// ESTO ES LO UNICO QUE HAY QUE CAMBIAR SEGUN EL HOST DONDE ESTE ESTE SCRIPT
// O SI SE QUIERE, TOMAR ESTOS VALORES DESDE UN ARCHIVO EXTERNO O DESDE UNA BD.
/////////////////////////////////////////////////////////////////////////////////
// define('LOGS_DIR', '/home/yordano/Projects/');
define('LOG_FILENAME_KEYWORD', 'dumbo-work');

//////////////////////////////////////////////////////////
// Variables requeridas en cualquier ambito
//////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// Funciones puras
//////////////////////////////////////////////////////////

function log_date() {
    return isset($_REQUEST['log']) ? $_REQUEST['log'] : false;
}

function page() {
    return isset($_REQUEST['p']) ? ($_REQUEST['p'] == '0' ? 1 : $_REQUEST['p']) : 1;
}

function client() {
    return isset($_REQUEST['c']) ? $_REQUEST['c'] : false;
}

function out_log_file(string $log_filename_keyword, string $rnd) {
    $temp = getenv('TEMP');
    return sprintf(
        "%s%s%s.%s.log",
        false === $temp ? '/tmp' : $temp,
        DIRECTORY_SEPARATOR,
        $log_filename_keyword, $rnd
    );
}

function out_tmp_log(string $log_filename_keyword, string $rnd) {
    $temp = getenv('TEMP');
    $tmp_log = sprintf(
        "%s%s%s.%s.tmp",
        false === $temp ? '/tmp' : $temp,
        DIRECTORY_SEPARATOR,
        $log_filename_keyword, $rnd
    );
    return $tmp_log;
}

function init_user_stat_object(string $line) {
    preg_match('/^Client\: (\d+)/', $line, $client_data);
    return [
        'client_id' => (int) $client_data[1]
    ];
}

function set_followed_count(array $client_data, string $line) {
    preg_match('/like firsts = \d+\): (\d+) \</', $line, $follows);
    return array_merge($client_data, [
        'followed' => $follows[1]
    ]);
}

function set_reference_profile(array $client_data, string $line) {
    preg_match('/nRef Profil: (.*)\</', $line, $prof_data);
    return array_merge($client_data, [
        'ref_prof' => $prof_data[1]
    ]);
}

function set_date_time(array $client_data, string $line) {
    preg_match('/Count: \d+ Hasnext: \d+ - (.*)\</', $line, $time_data);
    list($date, $time) = explode(' ', $time_data[1]);
    return array_merge($client_data, [
        'date' => $date,
        'time' => $time,
    ]);
}

function stats_count(string $log_file, string $keyword = 'client_id') {
    $output = trim(shell_exec("grep -c $keyword $log_file"));
    return (int) $output;
}

function get_client_stats(string $client, string $log_file) {
    $data = trim(shell_exec("grep $client $log_file"));
    $stats_array = preg_split('/\n/', $data);
    $client_stats_array = array_map(function($stat) {
        return json_decode($stat);
    }, $stats_array);
    return json_encode($client_stats_array);
}

function get_stats_page(int $page, string $log_file, int $page_size = 50) {
    $top = $page_size * $page;
    $head = trim(shell_exec("head -n $top $log_file | tail -n $page_size"));
    $array = preg_split('/\n/', $head);
    $total = stats_count($log_file);
    $objects = array_map(function($str) {
        return json_decode($str);
    }, $array);
    return json_encode(['total' => $total, 'page' => $page, 'stats' => $objects]);
}

//////////////////////////////////////////////////////////
// Funciones impuras
//////////////////////////////////////////////////////////

function rnd() {
    return mt_rand(0, 10) * mt_rand(10, 100) * mt_rand(100, 1000);
}

function clean_temps() {
    $temps = func_get_args();
    array_walk($temps, function($temp) {
        unlink($temp);
    });
}

/**
 * JUNTAR EN UN SOLO ARCHIVO LAS TRAZAS DE TODOS LOS WORKER. Puesto que los
 * parametros de esta funcion se explican por si solos, me restrinjo a explicar
 * solo los parametros $log_filename_keyword y $date. La magia se establece 
 * entre ellos dos. Solo pasar en $log_filename_keyword un trozo del nombre
 * del archivo. En cuanto a $date, si paso 201810 me devuelve la union de todo
 * lo que contenga octubre de 2018. Si paso 2018101 me devuelve del 10 al 19
 * de octubre del 2018. Esto da una idea de lo abarcador del filtro de este
 * parametro.
 */
function join_logs(string $logs_dir, string $log_filename_keyword,
                   string $date, string $out_log_file)
{
    shell_exec(
        sprintf(
            "cat %s%s%s*%s*log >> %s",
            $logs_dir, DIRECTORY_SEPARATOR, $log_filename_keyword,
            $date, $out_log_file
        )
    );
}

function output_client_data($out_log_file_handle, array $data = null) {
    if ($data !== null) {
        $json = json_encode($data);
        fputs($out_log_file_handle, $json . PHP_EOL);
    }
}

function collect_stats(string $out_log, string $tmp_log) {
    join_logs(LOGS_DIR, LOG_FILENAME_KEYWORD, log_date(), $out_log);
    
    // Abrir el archivo de entrada y el de salida
    $log_handle = fopen($out_log, "r");
    $tmp_handle = fopen($tmp_log, "w");
    if ($log_handle) {
        $stat = null;
        $c = 0;
        while ( ( $line = fgets($log_handle) ) !== false ) {
            if (preg_match('/^Client\: (\d+)/', $line, $client_data) === 1) {
                output_client_data($tmp_handle, $stat);
                $c++;
                $stat = init_user_stat_object($line);
            }
            // Cantidad de follows alcanzados para el cliente
            if (preg_match('/like firsts = \d+\): (\d+) \</', $line, $follows) === 1) {
                $stat = set_followed_count($stat, $line);
            }
            // Perfil de referencia
            if (preg_match('/^nRef Profil: /', $line) === 1) {
                $stat = set_reference_profile($stat, $line);
            }
            // Fecha y hora
            if (preg_match('/Count: \d+ Hasnext: \d+ - (.*)\</', $line, $time_data) === 1) {
                $stat = set_date_time($stat, $line);
            }
        }
        fclose($log_handle);
        fclose($tmp_handle);
    } else {
        echo 'Error al abrir archivo ' . LOG_FILE;
    }
}

function main() {
    $out_log = out_log_file(LOG_FILENAME_KEYWORD, rnd());
    $tmp_log = out_tmp_log(LOG_FILENAME_KEYWORD, rnd());

    collect_stats($out_log, $tmp_log);

    // Si existe el parametro $client, devuelvo solo las de ese cliente.
    if (client()) {
        $client_stats_json = get_client_stats(client(), $tmp_log);
        echo sprintf("{ \"stats\": %s }", $client_stats_json);
        clean_temps($out_log, $tmp_log);
        exit();
    }
    
    // Si se esta paginando, devuelvo solo la pagina pedida en el parametro p
    if (page()) {
        $stats_page = get_stats_page(page(), $tmp_log);
        echo $stats_page;
    }
    
    clean_temps($out_log, $tmp_log);
}

//////////////////////////////////////////////////////////
// Bloque principal
//////////////////////////////////////////////////////////

header('Content-Type: application/json');
main();
