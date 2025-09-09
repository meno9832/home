<?php 
// DB 연결
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset(DB_CHARSET);

// 사이트 설정 불러오기
$sql = "SELECT favicon, site_title FROM " . DB_PREFIX ."site_setting WHERE id=1 LIMIT 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();
$conn->close();
?>

<html lang="ko">
<head>
    <link rel="icon" href="<?= htmlspecialchars($settings['favicon']) ?>">
    <title><?= htmlspecialchars($settings['site_title']) ?></title>
</head>