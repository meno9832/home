<?php
define('IN_ADMIN', true);
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


if ($table == 'main_module') {
    if($action === 'update' && $id){
            $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET x=?, y=?, width=?, height=? WHERE id=?");
            $stmt->execute([$_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'], $_POST['id']]);
            echo 'ok';

    } elseif($action === 'delete' && $id){
            $stmt = $pdo->prepare("DELETE FROM " . DB_PREFIX . "main_module WHERE id=?");
            $stmt->execute([$_POST['id']]);
            echo json_encode(['status'=>'ok']);;
    } elseif($action === 'update_grid' && $id){
            $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET width=? WHERE id=1");
            $stmt->execute([$_POST['size']]);
            echo 'ok';
    } elseif($action === 'add'){
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
} elseif ($table == 'board_group') {
    $ids        = $_POST['id'] ?? [];
    $table_ids  = $_POST['table_id'] ?? [];
    $names      = $_POST['name'] ?? [];
    $auth_roles = $_POST['auth_role'] ?? [];
} elseif ($table == 'board') {
    $ids        = $_POST['id'] ?? [];
    $table_ids  = $_POST['table_id'] ?? [];
    $names      = $_POST['name'] ?? [];
    $group_ids  = $_POST['group_id'] ?? [];
    $skins      = $_POST['skin'] ?? [];
}





if ($table === 'board_group') {

    if($action === 'delete' && !empty($_POST['id'])){
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM " . DB_PREFIX . "board_group WHERE id=?");
        if($stmt->execute([$id])){
            echo json_encode(['ok' => true]);
        } else {
            echo 'fail';
        }
        exit;
    }

    $ids        = $_POST['id'] ?? [];
    $table_ids  = $_POST['table_id'] ?? [];
    $names      = $_POST['name'] ?? [];
    $auth_roles = $_POST['auth_role'] ?? [];

    if ($ids && count($ids) === count($table_ids) && count($ids) === count($names) && count($ids) === count($auth_roles)) {

        // UPDATE 준비 (기존 행)
        $stmtUpdate = $pdo->prepare("UPDATE " . DB_PREFIX . "board_group 
                                     SET table_id=?, name=?, auth_role=? 
                                     WHERE id=?");

        // INSERT 준비 (신규 행)
        $stmtInsert = $pdo->prepare("INSERT INTO " . DB_PREFIX . "board_group (table_id, name, auth_role) 
                                     VALUES (?, ?, ?)");

        foreach ($ids as $i => $id) {
            $table_id = $table_ids[$i];
            $name     = $names[$i];
            $auth     = $auth_roles[$i];

            if ($id) { 
                // 기존 행 → UPDATE
                $stmtUpdate->execute([$table_id, $name, $auth, $id]);
            } else {  
                // 신규 행 → INSERT
                $stmtInsert->execute([$table_id, $name, $auth]);
            }
        }

        // 저장 완료 후 원래 페이지로 리다이렉트
        header("Location: /admin?page=bo_group");
        exit;
    } else {
        echo "error: invalid data";
    }
}

if($table === 'board'){

    if($action === 'delete' && !empty($_POST['id'])){
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM " . DB_PREFIX . "board WHERE id=?");
        if($stmt->execute([$id])){
            echo json_encode(['ok' => true]);
        } else {
            echo 'fail';
        }
        exit;
    }

    $ids        = $_POST['id'] ?? [];
    $table_ids  = $_POST['table_id'] ?? [];
    $names      = $_POST['name'] ?? [];
    $group_ids  = $_POST['group_id'] ?? [];
    $skins      = $_POST['skin'] ?? [];

    error_log("SAVE POST: " . print_r($_POST, true));

        // UPDATE 준비 (기존 행)
    $stmtUpdate = $pdo->prepare("UPDATE " . DB_PREFIX . "board 
                                SET table_id=?, name=?, group_id=?, skin=? 
                                WHERE id=?");

        // INSERT 준비 (신규 행)
    $stmtInsert = $pdo->prepare("INSERT INTO " . DB_PREFIX . "board (table_id, name, group_id, skin) 
                                 VALUES (?, ?, ?, ?)");

    foreach ($ids as $i => $id) {
        $table_id = $table_ids[$i];
        $name     = $names[$i];
        $group_id = $group_ids[$i];
        $skin     = $skins[$i];

        if ($id) { 
            // 기존 행 → UPDATE
            $stmtUpdate->execute([$table_id, $name, $group_id, $skin, $id]);
        } else {  
            // 신규 행 → INSERT
            // 신규 행 → INSERT 전에 table_id 중복 체크
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM " . DB_PREFIX . "board WHERE table_id=?");
            $stmtCheck->execute([$table_id]);
            $exists = $stmtCheck->fetchColumn();

            if ($exists > 0) {
                // 중복 → 에러 응답
                echo json_encode(['ok'=>false, 'msg'=>'이미 존재하는 table_id 입니다.']);
                exit;
            }

            // 중복 없음 → INSERT
            $stmtInsert->execute([$table_id, $name, $group_id, $skin]);

            // 게시판 테이블 생성 (간단 예시)
            $sql = "CREATE TABLE " . DB_PREFIX . "board_". $table_id . " (
                id INT AUTO_INCREMENT PRIMARY KEY,
                subject VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                writer VARCHAR(100),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

            $pdo->exec($sql);
        }
    }

        // 저장 완료 후 원래 페이지로 리다이렉트
    header("Location: /admin?page=bo_board");
    exit;
    
}