<?php
require_once PATH .'/data/dbconfig.php';

if (!defined('IN_ADMIN')) {
    require_once PATH . '/index.php'; 
    exit;
}
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// 그리드 크기 가져오기 (id=1)
$grid_size = $pdo->query("SELECT width FROM " . DB_PREFIX . "main_module WHERE id=1")->fetchColumn();
if(!$grid_size) $grid_size = 50;

// 모듈 가져오기 (id!=1)
$modules = $pdo->query("SELECT * FROM " . DB_PREFIX . "main_module WHERE id!=1")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Grid Editor</title>
<style>
body { margin:0; font-family:sans-serif; }
#toolbar { padding:5px; background:#eee; display:flex; gap:5px; align-items:center; }
#grid {
    position: relative;
    width: 1000px;
    height: 800px;
    background-color: #f0f0f0;
    background-image:
        linear-gradient(to right, #ccc 1px, transparent 1px),
        linear-gradient(to bottom, #ccc 1px, transparent 1px);
    background-size: 50px 50px; /* 그리드 크기 */
}
.module { position:absolute; border:1px solid #333; background:#fff; box-sizing:border-box; overflow:hidden; }
.module .controls { position:absolute; top:2px; right:2px; display:flex; gap:2px; }
.module .controls button { font-size:12px; cursor:pointer; }
</style>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>
<body>

<div id="toolbar">
    그리드 크기: <input type="number" id="gridSizeInput" value="<?= $grid_size ?>" style="width:60px;">
    <button id="saveGridSize">저장</button>
    <button id="addModule">모듈 추가</button>
</div>

<div id="grid"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
let GRID_SIZE = <?= $grid_size ?>;
let MODULES = <?= json_encode($modules) ?>;
</script>
<script src="<?= PATH_JS ?>/grid-editor.js"></script>

</body>
</html>
