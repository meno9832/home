<?php
require_once __DIR__ .'/../../data/dbconfig.php';
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

ini_set('display_errors', 0); // HTML로 에러 출력 금지
error_reporting(E_ALL);  
header('Content-Type: application/json; charset=utf-8');


$action = $_POST['action'] ?? '';
$table  = $_POST['table'] ?? '';
$id     = $_POST['id'] ?? null;

if($action === 'update' && $id){
    if ($table == DB_PREFIX .'main_module') {
        $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET x=?, y=?, width=?, height=? WHERE id=?");
        $stmt->execute([$_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'], $_POST['id']]);
        echo 'ok';
    }
} elseif($action === 'delete' && $id){
    if ($table == DB_PREFIX .'main_module') {
        $stmt = $pdo->prepare("DELETE FROM " . DB_PREFIX . "main_module WHERE id=?");
        $stmt->execute([$_POST['id']]);
        echo 'ok';
    }
} elseif($action === 'update_grid' && $id){
    if ($table == DB_PREFIX .'main_module') {
        $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET width=? WHERE id=1");
        $stmt->execute([$_POST['size']]);
        echo 'ok';
    }
} elseif($action === 'add'){
    if ($table == DB_PREFIX .'main_module') {
        $table = DB_PREFIX . "main_module";       // 사용할 테이블
        $name  = $_POST['name'] ?? '';

        if (!$name) {
            echo json_encode(['status' => 'error', 'message' => 'name 값이 필요합니다.']);
            exit;
        }

        // INSERT 실행
        $stmt = $pdo->prepare("INSERT INTO `$table` (name, width, height, x, y) VALUES (?, 100, 100, 0, 0)");
        $stmt->execute([$name]);
        $id = $pdo->lastInsertId();

        // 새로 추가된 행 조회 (Prepared Statement)
        $stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
        $stmt->execute([$id]);
        $newModule = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'ok', 'data' => $newModule], JSON_UNESCAPED_UNICODE);
    }
}
