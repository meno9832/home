<?php
$page = basename($_GET['page'] ?? 'dashboard');  // 기본 페이지
?>
<div id="main">
    <?php
    $file = __DIR__ . "/{$page}.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<h2>페이지를 찾을 수 없습니다.</h2>";
    }
    ?>
</div>