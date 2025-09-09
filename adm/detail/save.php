<?php
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

$action = $_POST['action'] ?? '';

if($action === 'update'){
    $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET x=?, y=?, width=?, height=? WHERE id=?");
    $stmt->execute([$_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'], $_POST['id']]);
    echo 'ok';
} elseif($action === 'delete'){
    $stmt = $pdo->prepare("DELETE FROM " . DB_PREFIX . "main_module WHERE id=?");
    $stmt->execute([$_POST['id']]);
    echo 'ok';
} elseif($action === 'update_grid'){
    $stmt = $pdo->prepare("UPDATE " . DB_PREFIX . "main_module SET width=? WHERE id=1");
    $stmt->execute([$_POST['size']]);
    echo 'ok';
} elseif($action === 'add'){
    $stmt = $pdo->prepare("INSERT INTO " . DB_PREFIX . "main_module (name, width, height, x, y) VALUES (?, 100, 100, 0, 0)");
    $stmt->execute([$_POST['name']]);
    $id = $pdo->lastInsertId();
    $stmt = $pdo->query("SELECT * FROM " . DB_PREFIX . "main_module WHERE id=$id");
    $newModule = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($newModule);
}
