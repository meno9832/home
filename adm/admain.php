<?php
$page = $_GET['page'] ?? 'dashboard';  // 기본 페이지
$detail = $_GET['detail'] ?? null;
$file = __DIR__ . "/{$page}.php";
?>
<div id="main">
    <?php
    if (file_exists($file)) {
        if ($detail) {
            include __DIR__ ."/detail/{$detail}.php";
        } else {
            include $file;
        }
    } else {
        echo "<h2>페이지를 찾을 수 없습니다.</h2>";
    }
    ?>
</div>