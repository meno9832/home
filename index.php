<?php
// DB 설정 확인
if (!file_exists(__DIR__ . '/data/dbconfig.php')) {
    header("Location: install.php");
    exit;
}

// DB 설정 불러오기
require_once __DIR__ . '/data/dbconfig.php';

// CMS 환경설정
require_once __DIR__ . '/config.php';

// 라우터 실행
require_once __DIR__ . '/core/Router.php';
$router = new Router();
$router->dispatch();
