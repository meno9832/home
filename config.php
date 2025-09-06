<?php

if (PHP_VERSION >= '5.1.0') {
    //if (function_exists("date_default_timezone_set")) date_default_timezone_set("Asia/Seoul");
    date_default_timezone_set("Asia/Seoul");
}

define('DOMAIN_PATH', '');    
define('HTTPS_DOMAIN_PATH', '');  

if (DOMAIN_PATH) {
    define('URL_PATH', G5_DOMAIN);
} else {
    // 로컬/자동 환경 대응
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    define('URL_PATH', $protocol . '://' . $host . $script);
}

// 서버 파일 절대 경로
if (isset($path['path'])) {
    define('PATH', $path['path']);
} else {
    define('PATH', __DIR__); // 현재 폴더 기준
}


define('PATH_CSS', URL_PATH . '/resourse/css');
define('PATH_JS', URL_PATH . '/resourse/js');
?>