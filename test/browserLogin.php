<?php

define('IS_CLI', PHP_SAPI === 'cli' or defined('STDIN'));
define('ARGS', $argv);
define('REQUEST', $_REQUEST);

// funciones puras

function username() {
    return IS_CLI ? ARGS[1] : REQUEST['username'];
}

function password() {
    return IS_CLI ? ARGS[2] : REQUEST['password'];
}

function cookies_filename(string $rnd) {
    $temp = false === getenv('TEMP') ? '/tmp' : getenv('TEMP');
    return sprintf("%s/cookies.%s.txt", $temp, $rnd);
}

function get_cookie(string $cookie_name, string $cookies_file) {
    $raw_cookies_data = file_get_contents($cookies_file);
    $lines_array = explode(PHP_EOL, $raw_cookies_data);
    $cookie = array_reduce($lines_array, function($cookie, $line) use ($cookie_name) {
        if (strstr($line, $cookie_name) !== false) {
            $splitted = explode($cookie_name, $line);
            return trim(end($splitted));
        }
        return $cookie;
    }, '');
    if ('' !== $cookie) {
        return $cookie;
    }
    throw new \Exception("No $cookie_name cookie was found at the file cookies file");
}

// funciones impuras

function rnd() {
    return sprintf(
        "%s-%s-%s",
        uniqid(),
        uniqid(),
        uniqid()
    );
}

function user_agent() {
    $last_until_now = 62;
    $v = mt_rand(58, $last_until_now);
    return "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:$v.0) Gecko/20100101 Firefox/$v.0";
}

function instagram_com(string $u_agent, string $cookies_file) {
    $cmd = "curl 'https://www.instagram.com/' " .
        "-s -c $cookies_file " .
        "-H 'User-Agent: $u_agent' " .
        "-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' " .
        "-H 'Accept-Language: es,en-US;q=0.7,en;q=0.3' " .
        "-H 'DNT: 1' " .
        "-H 'Connection: keep-alive' " .
        "-H 'Upgrade-Insecure-Requests: 1'";
    exec($cmd, $output, $ret);
    if (0 !== $ret) { throw new \Exception('Unable to fetch https://www.instagram.com'); }
    return implode(PHP_EOL, $output);
}

function batch_fetch_web(string $u_agent, string $cookies_file) {
    $csrftoken = get_cookie('csrftoken', $cookies_file);
    $mid = get_cookie('mid', $cookies_file);
    $rur = get_cookie('rur', $cookies_file);
    $mcd = get_cookie('mcd', $cookies_file);
    $cmd = "curl 'https://www.instagram.com/qp/batch_fetch_web/' " .
        "-s " .
        "-H 'User-Agent: $u_agent' " .
        "-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' " .
        "-H 'Accept-Language: es,en-US;q=0.7,en;q=0.3' " .
        "-H 'Referer: https://www.instagram.com/' " .
        "-H 'X-CSRFToken: $csrftoken' " .
        "-H 'X-Instagram-AJAX: d4e4c9fdb67b' " .
        "-H 'Content-Type: application/x-www-form-urlencoded' " .
        "-H 'X-Requested-With: XMLHttpRequest' " .
        "-H 'Cookie: mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken' " .
        "-H 'DNT: 1' " .
        "-H 'Connection: keep-alive' " .
        "-H 'TE: Trailers' " .
        "--data 'surfaces_to_queries=%7B%225095%22%3A%22viewer()+%7B%5Cn++eligible_promotions.surface_nux_id(%3Csurface%3E).external_gating_permitted_qps(%3Cexternal_gating_permitted_qps%3E)+%7B%5Cn++++edges+%7B%5Cn++++++priority%2C%5Cn++++++time_range+%7B%5Cn++++++++start%2C%5Cn++++++++end%5Cn++++++%7D%2C%5Cn++++++node+%7B%5Cn++++++++id%2C%5Cn++++++++promotion_id%2C%5Cn++++++++max_impressions%2C%5Cn++++++++triggers%2C%5Cn++++++++template+%7B%5Cn++++++++++name%2C%5Cn++++++++++parameters+%7B%5Cn++++++++++++name%2C%5Cn++++++++++++string_value%5Cn++++++++++%7D%5Cn++++++++%7D%2C%5Cn++++++++creatives+%7B%5Cn++++++++++title+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++content+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++footer+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++social_context+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++primary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++secondary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++dismiss_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++image+%7B%5Cn++++++++++++uri%5Cn++++++++++%7D%5Cn++++++++%7D%5Cn++++++%7D%5Cn++++%7D%5Cn++%7D%5Cn%7D%22%2C%225780%22%3A%22viewer()+%7B%5Cn++eligible_promotions.surface_nux_id(%3Csurface%3E).external_gating_permitted_qps(%3Cexternal_gating_permitted_qps%3E)+%7B%5Cn++++edges+%7B%5Cn++++++priority%2C%5Cn++++++time_range+%7B%5Cn++++++++start%2C%5Cn++++++++end%5Cn++++++%7D%2C%5Cn++++++node+%7B%5Cn++++++++id%2C%5Cn++++++++promotion_id%2C%5Cn++++++++max_impressions%2C%5Cn++++++++triggers%2C%5Cn++++++++template+%7B%5Cn++++++++++name%2C%5Cn++++++++++parameters+%7B%5Cn++++++++++++name%2C%5Cn++++++++++++string_value%5Cn++++++++++%7D%5Cn++++++++%7D%2C%5Cn++++++++creatives+%7B%5Cn++++++++++title+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++content+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++footer+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++social_context+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++primary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++secondary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++dismiss_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++image+%7B%5Cn++++++++++++uri%5Cn++++++++++%7D%5Cn++++++++%7D%5Cn++++++%7D%5Cn++++%7D%5Cn++%7D%5Cn%7D%22%7D&vc_policy=default&version=1'";
    exec($cmd, $output, $ret);
    if (0 !== $ret) {
        throw new \Exception('Unable to request qp/batch_fetch_web/');
    }
    return implode(PHP_EOL, $output);
}

function ajax_bz(string $u_agent, string $cookies_file) {
    $csrftoken = get_cookie('csrftoken', $cookies_file);
    $mid = get_cookie('mid', $cookies_file);
    $rur = get_cookie('rur', $cookies_file);
    $mcd = get_cookie('mcd', $cookies_file);
    $cmd = "curl 'https://www.instagram.com/ajax/bz' " .
        "-s " .
        "-H 'User-Agent: $u_agent' " .
        "-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' " .
        "-H 'Accept-Language: es,en-US;q=0.7,en;q=0.3' " .
        "-H 'Referer: https://www.instagram.com/' " .
        "-H 'X-CSRFToken: $csrftoken' " .
        "-H 'X-Instagram-AJAX: d4e4c9fdb67b' " .
        "-H 'Content-Type: application/x-www-form-urlencoded' " .
        "-H 'X-Requested-With: XMLHttpRequest' " .
        "-H 'Cookie: mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken' " .
        "-H 'DNT: 1' " .
        "-H 'Connection: keep-alive' " .
        "-H 'TE: Trailers' " .
        "--data 'q=%5B%7B%22page_id%22%3A%22b8nr8x%22%2C%22posts%22%3A%5B%5B%22qe%3Aexpose%22%2C%7B%22qe%22%3A%22su_universe%22%2C%22mid%22%3A%22%s%22%7D%2C1540315780568%2C0%5D%5D%2C%22trigger%22%3A%22qe%3Aexpose%22%2C%22send_method%22%3A%22ajax%22%7D%5D&ts=1540315788333'";
    exec($cmd, $output, $ret);
    if (0 !== $ret) {
        throw new \Exception('Unable to request ajax/bz');
    }
    return implode(PHP_EOL, $output);
}

function accounts_login(string $u_agent, string $cookies_file) {
    $csrftoken = get_cookie('csrftoken', $cookies_file);
    $mid = get_cookie('mid', $cookies_file);
    $rur = get_cookie('rur', $cookies_file);
    $mcd = get_cookie('mcd', $cookies_file);
    $cmd = "curl 'https://www.instagram.com/accounts/login/?__a=1' " .
        "-s " .
        "-H 'User-Agent: $u_agent' " .
        "-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' " .
        "-H 'Accept-Language: es,en-US;q=0.7,en;q=0.3' " .
        "-H 'Referer: https://www.instagram.com/accounts/login/?source=auth_switcher' " .
        "-H 'X-Instagram-GIS: 5fe7d1651104a6619e4db3b4be631fd9' " .
        "-H 'X-Requested-With: XMLHttpRequest' " .
        "-H 'Cookie: mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken' " .
        "-H 'DNT: 1' " .
        "-H 'Connection: keep-alive' " .
        "-H 'TE: Trailers'";
    exec($cmd, $output, $ret);
    if (0 !== $ret) {
        throw new \Exception('Unable to request accounts/login');
    }
    return implode(PHP_EOL, $output);
}

function accounts_login_ajax(string $u_agent, string $cookies_file) {
    $user = username();
    $pass = password();
    $csrftoken = get_cookie('csrftoken', $cookies_file);
    $mid = get_cookie('mid', $cookies_file);
    $rur = get_cookie('rur', $cookies_file);
    $mcd = get_cookie('mcd', $cookies_file);
    $cmd = "curl 'https://www.instagram.com/accounts/login/ajax/' " .
        "-s -c $cookies_file " .
        "-H 'User-Agent: $u_agent' " .
        "-H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' " .
        "-H 'Accept-Language: es,en-US;q=0.7,en;q=0.3' " .
        "-H 'Referer: https://www.instagram.com/accounts/login/?source=auth_switcher' " .
        "-H 'X-CSRFToken: $csrftoken' " .
        "-H 'X-Instagram-AJAX: d4e4c9fdb67b' " .
        "-H 'Content-Type: application/x-www-form-urlencoded' " .
        "-H 'X-Requested-With: XMLHttpRequest' " .
        "-H 'Cookie: mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken' " .
        "-H 'DNT: 1' " .
        "-H 'Connection: keep-alive' " .
        "-H 'TE: Trailers' " .
        "--data 'username=$user&password=$pass&queryParams=%7B%22source%22%3A%22auth_switcher%22%7D'";
    exec($cmd, $output, $ret);
    if (0 !== $ret) {
        throw new \Exception('Unable to request accounts/login/ajax');
    }
    return implode(PHP_EOL, $output);
}

// punto de entrada

function main() {
    $ua = user_agent();
    $cookies = cookies_filename(rnd());
    instagram_com($ua, $cookies) . PHP_EOL;
    sleep(mt_rand(1,5));
    batch_fetch_web($ua, $cookies) . PHP_EOL;
    sleep(mt_rand(1,5));
    ajax_bz($ua, $cookies) . PHP_EOL;
    sleep(mt_rand(1,5));
    accounts_login($ua, $cookies) . PHP_EOL;
    sleep(mt_rand(1,5));
    echo accounts_login_ajax($ua, $cookies) . PHP_EOL;
}

main();
