<?php
require __DIR__ . '/../vendor/autoload.php';

use \GuzzleHttp\Client;
use \GuzzleHttp\Cookie\SetCookie;
use \GuzzleHttp\Cookie\CookieJar;
use \GuzzleHttp\Psr7\Request;
use \GuzzleHttp\Psr7\Response;

$GLOBALS['client'] = new Client([
    'cookies' => new CookieJar,
    'debug' => false
]);

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
    return sprintf(
        "%s%scookies.%s.txt",
        $temp, DIRECTORY_SEPARATOR, $rnd
    );
}

function get_cookie(string $cookie_name, array $cookies) {
    $reducer = function ($cookie, $current_cookie) use ($cookie_name) {
        $cookie_object = (object)$current_cookie;
        return $cookie_object->Name === 'csrftoken' ? $cookie_object->Value : $cookie;
    };
    return array_reduce($cookies, $reducer, null);
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

$GLOBALS['ua'] = user_agent();

function instagram_com() {
    $client = $GLOBALS['client'];
    $response = $client->send(
        new Request('GET', 'https://www.instagram.com', [
            "User-Agent" => $GLOBALS['ua'],
            "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            "Accept-Language" => 'es,en-US;q=0.7,en;q=0.3',
            "DNT" => 1,
            "Connection" => 'keep-alive',
            "Upgrade-Insecure-Requests" => 1,
        ])
    );
    return $response->getBody()->getContents();
}

function batch_fetch_web() {
    $client = $GLOBALS['client'];
    $cookies = $client->getConfig('cookies')->toArray();
    $csrftoken = get_cookie('csrftoken', $cookies);
    $mid = get_cookie('mid', $cookies);
    $rur = get_cookie('rur', $cookies);
    $mcd = get_cookie('mcd', $cookies);
    $response = $client->send(
        new Request('POST', 'https://www.instagram.com/qp/batch_fetch_web/',
            [
                "User-Agent" => $GLOBALS['ua'],
                "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                "Accept-Language" => 'es,en-US;q=0.7,en;q=0.3',
                "Referer" => 'https://www.instagram.com/',
                "X-CSRFToken" => $csrftoken,
                "X-Instagram-AJAX" => 'd4e4c9fdb67b',
                "Content-Type" => 'application/x-www-form-urlencoded',
                "X-Requested-With" => 'XMLHttpRequest',
                "Cookie" => "mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken",
                "DNT" => '1',
                "TE" => 'Trailers',
            ],
            'surfaces_to_queries=%7B%225095%22%3A%22viewer()+%7B%5Cn++eligible_promotions.surface_nux_id(%3Csurface%3E).external_gating_permitted_qps(%3Cexternal_gating_permitted_qps%3E)+%7B%5Cn++++edges+%7B%5Cn++++++priority%2C%5Cn++++++time_range+%7B%5Cn++++++++start%2C%5Cn++++++++end%5Cn++++++%7D%2C%5Cn++++++node+%7B%5Cn++++++++id%2C%5Cn++++++++promotion_id%2C%5Cn++++++++max_impressions%2C%5Cn++++++++triggers%2C%5Cn++++++++template+%7B%5Cn++++++++++name%2C%5Cn++++++++++parameters+%7B%5Cn++++++++++++name%2C%5Cn++++++++++++string_value%5Cn++++++++++%7D%5Cn++++++++%7D%2C%5Cn++++++++creatives+%7B%5Cn++++++++++title+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++content+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++footer+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++social_context+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++primary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++secondary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++dismiss_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++image+%7B%5Cn++++++++++++uri%5Cn++++++++++%7D%5Cn++++++++%7D%5Cn++++++%7D%5Cn++++%7D%5Cn++%7D%5Cn%7D%22%2C%225780%22%3A%22viewer()+%7B%5Cn++eligible_promotions.surface_nux_id(%3Csurface%3E).external_gating_permitted_qps(%3Cexternal_gating_permitted_qps%3E)+%7B%5Cn++++edges+%7B%5Cn++++++priority%2C%5Cn++++++time_range+%7B%5Cn++++++++start%2C%5Cn++++++++end%5Cn++++++%7D%2C%5Cn++++++node+%7B%5Cn++++++++id%2C%5Cn++++++++promotion_id%2C%5Cn++++++++max_impressions%2C%5Cn++++++++triggers%2C%5Cn++++++++template+%7B%5Cn++++++++++name%2C%5Cn++++++++++parameters+%7B%5Cn++++++++++++name%2C%5Cn++++++++++++string_value%5Cn++++++++++%7D%5Cn++++++++%7D%2C%5Cn++++++++creatives+%7B%5Cn++++++++++title+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++content+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++footer+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++social_context+%7B%5Cn++++++++++++text%5Cn++++++++++%7D%2C%5Cn++++++++++primary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++secondary_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++dismiss_action%7B%5Cn++++++++++++title+%7B%5Cn++++++++++++++text%5Cn++++++++++++%7D%2C%5Cn++++++++++++url%2C%5Cn++++++++++++limit%2C%5Cn++++++++++++dismiss_promotion%5Cn++++++++++%7D%2C%5Cn++++++++++image+%7B%5Cn++++++++++++uri%5Cn++++++++++%7D%5Cn++++++++%7D%5Cn++++++%7D%5Cn++++%7D%5Cn++%7D%5Cn%7D%22%7D&vc_policy=default&version=1'
        )
    );
    return $response->getBody()->getContents();
}

function ajax_bz() {
    $ua = $GLOBALS['ua'];
    $client = $GLOBALS['client'];
    $cookies = $client->getConfig('cookies')->toArray();
    $csrftoken = get_cookie('csrftoken', $cookies);
    $mid = get_cookie('mid', $cookies);
    $rur = get_cookie('rur', $cookies);
    $mcd = get_cookie('mcd', $cookies);
    $response = $client->send(
        new Request(
            'POST',
            'https://www.instagram.com/ajax/bz',
            [
                "User-Agent" => $GLOBALS['ua'],
                "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                "Accept-Language" => 'es,en-US;q=0.7,en;q=0.3',
                "Referer" => 'https://www.instagram.com/',
                "X-CSRFToken" => $csrftoken,
                "X-Instagram-AJAX" => 'd4e4c9fdb67b',
                "Content-Type" => 'application/x-www-form-urlencoded',
                "X-Requested-With" => 'XMLHttpRequest',
                "Cookie" => "mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken",
                "DNT" => '1',
                "TE" => 'Trailers',
            ],
            'q=%5B%7B%22page_id%22%3A%22b8nr8x%22%2C%22posts%22%3A%5B%5B%22qe%3Aexpose%22%2C%7B%22qe%22%3A%22su_universe%22%2C%22mid%22%3A%22%s%22%7D%2C1540315780568%2C0%5D%5D%2C%22trigger%22%3A%22qe%3Aexpose%22%2C%22send_method%22%3A%22ajax%22%7D%5D&ts=1540315788333'
        )
    );
    return $response->getBody()->getContents();
}

function accounts_login() {
    $ua = $GLOBALS['ua'];
    $client = $GLOBALS['client'];
    $cookies = $client->getConfig('cookies')->toArray();
    $csrftoken = get_cookie('csrftoken', $cookies);
    $mid = get_cookie('mid', $cookies);
    $rur = get_cookie('rur', $cookies);
    $mcd = get_cookie('mcd', $cookies);
    $response = $client->send(
        new Request(
            'GET',
            'https://www.instagram.com/accounts/login/?__a=1',
            [
                "User-Agent" => $GLOBALS['ua'],
                "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                "Accept-Language" => 'es,en-US;q=0.7,en;q=0.3',
                "Referer" => 'https://www.instagram.com/',
                "X-Instagram-GIS" => '5fe7d1651104a6619e4db3b4be631fd9',
                "X-Requested-With" => 'XMLHttpRequest',
                "Cookie" => "mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken",
                "DNT" => '1',
                "TE" => 'Trailers',
            ]
        )
    );
    return $response->getBody()->getContents();
}

function accounts_login_ajax() {
    $user = username();
    $pass = password();
    $ua = $GLOBALS['ua'];
    $client = $GLOBALS['client'];
    $cookies = $client->getConfig('cookies')->toArray();
    $csrftoken = get_cookie('csrftoken', $cookies);
    $mid = get_cookie('mid', $cookies);
    $rur = get_cookie('rur', $cookies);
    $mcd = get_cookie('mcd', $cookies);
    $response = $client->send(
        new Request(
            'POST',
            'https://www.instagram.com/accounts/login/ajax/',
            [
                "User-Agent" => $GLOBALS['ua'],
                "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                "Accept-Language" => 'es,en-US;q=0.7,en;q=0.3',
                "Referer" => 'https://www.instagram.com/',
                "X-CSRFToken" => $csrftoken,
                "X-Instagram-AJAX" => 'd4e4c9fdb67b',
                "X-Requested-With" => 'XMLHttpRequest',
                "Cookie" => "mid=$mid; rur=$rur; mcd=$mcd; csrftoken=$csrftoken",
                "Content-Type" => "application/x-www-form-urlencoded",
                "DNT" => '1',
                "TE" => 'Trailers',
            ],
            "username=$user&password=$pass&queryParams=%7B%22source%22%3A%22auth_switcher%22%7D"
        )
    );
    return $response->getBody()->getContents();
}

// punto de entrada

function main() {
    instagram_com();
    sleep(mt_rand(1,2));
    batch_fetch_web();
    sleep(mt_rand(1,2));
    ajax_bz();
    sleep(mt_rand(1,2));
    accounts_login();
    sleep(mt_rand(1,2));
    echo accounts_login_ajax();
    if(true)die();
}

main();
