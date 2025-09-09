<?php
// 설치 여부 확인
if (file_exists(__DIR__ . '/data/dbconfig.php')) {
    die("이미 설치가 완료되었습니다. <a href='/'>사이트로 이동</a>");
}

// 설치 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host   = trim($_POST['db_host']);
    $db_port   = (int)$_POST['db_port'];
    $db_user   = trim($_POST['db_user']);
    $db_pass   = trim($_POST['db_pass']);
    $db_name   = trim($_POST['db_name']);
    $db_prefix = trim($_POST['db_prefix']) ?: 'cms_';

    // DB 연결 테스트
    $conn = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
    if ($conn->connect_error) {
        die("❌ DB 연결 실패: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // 기본 users 테이블 생성
    $sql_users = "
        CREATE TABLE IF NOT EXISTS `{$db_prefix}users` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            role TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    if (!$conn->query($sql_users)) {
        die("❌ users 테이블 생성 실패: " . $conn->error);
    }
    $sql_main_module = "
        CREATE TABLE IF NOT EXISTS `{$db_prefix}main_module` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            width INT NOT NULL,
            height INT NOT NULL,
            x INT NOT NULL,
            y INT NOT NULL,
            type VARCHAR(50),
            con1 TEXT, con2 TEXT, con3 TEXT, con4 TEXT, con5 TEXT, con6 TEXT,
            con7 TEXT, con8 TEXT, con9 TEXT, con10 TEXT, con11 TEXT,
            con12 TEXT, con13 TEXT, con14 TEXT, con15 TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if (!$conn->query($sql_main_module)) {
        die("❌ main_module 테이블 생성 실패: " . $conn->error);
    }

    // grid_setting 초기값 입력 (없으면 추가, 있으면 무시)
    $sql_insert = "
        INSERT INTO `{$db_prefix}main_module` (id, name, width, height, x, y)
        VALUES (1, 'grid_setting', 50, 50, 0, 0)
        ON DUPLICATE KEY UPDATE width=VALUES(width), height=VALUES(height);
    ";

    if (!$conn->query($sql_insert)) {
        die("❌ grid_setting 초기값 입력 실패: " . $conn->error);
    }

    $sql_site_setting = "
        CREATE TABLE IF NOT EXISTS `{$db_prefix}site_setting` (
            id INT AUTO_INCREMENT PRIMARY KEY,
            is_public TINYINT(1) NOT NULL DEFAULT 0,
            allow_account_create TINYINT(1) NOT NULL DEFAULT 0,
            allow_character_create TINYINT(1) NOT NULL DEFAULT 0,
            allow_character_edit TINYINT(1) NOT NULL DEFAULT 0,
            site_title VARCHAR(255) NOT NULL,
            site_description TEXT,
            favicon VARCHAR(255) DEFAULT NULL,
            main_image VARCHAR(255) DEFAULT NULL,
            bgm VARCHAR(255) DEFAULT NULL,
            twitter_widget TEXT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    if (!$conn->query($sql_site_setting)) {
        die("❌ users 테이블 생성 실패: " . $conn->error);
    }
    $sql_insert = "
        INSERT INTO {$db_prefix}site_setting (id, is_public, site_title, site_description) 
        VALUES (1, 1, '내 홈페이지', '사이트 설명입니다.')
        ON DUPLICATE KEY UPDATE id=1;
    ";

    if (!$conn->query($sql_insert)) {
        die("❌ grid_setting 초기값 입력 실패: " . $conn->error);
    }

    // 관리자 계정 생성 (이름 + 비밀번호)
    $admin_name = $_POST['admin_name'];
    $admin_pass = password_hash($_POST['admin_pass'], PASSWORD_BCRYPT);

    $conn->query("INSERT INTO `{$db_prefix}users` 
        (username, password, role) VALUES 
        ('$admin_name', '$admin_pass', 4)
    ");

    // dbconfig.php 파일 내용
    $dbconfig_content = "<?php\n"
        ."define('DB_HOST', '$db_host');\n"
        ."define('DB_PORT', $db_port);\n"
        ."define('DB_USER', '$db_user');\n"
        ."define('DB_PASS', '$db_pass');\n"
        ."define('DB_NAME', '$db_name');\n"
        ."define('DB_CHARSET', 'utf8mb4');\n"
        ."define('DB_COLLATE', '');\n"
        ."define('DB_PREFIX', '$db_prefix');\n";

    // /data 폴더 없으면 생성
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }

    // dbconfig.php 생성
    file_put_contents(__DIR__ . '/data/dbconfig.php', $dbconfig_content);

    // .htaccess 생성 (data 폴더 접근 제한)
    $htaccess_content = "<Files *>\nOrder Allow,Deny\nDeny from all\n</Files>";
    file_put_contents(__DIR__ . '/data/.htaccess', $htaccess_content);

    echo "<h2>✅ 설치 완료!</h2>";
    echo "<p><a href='index.php'>사이트로 이동</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>CMS 설치</title>
</head>
<body>
    <h2>CMS 설치 마법사</h2>
    <form method="post">
        <h3>데이터베이스 설정</h3>
        <label>DB Host: <input type="text" name="db_host" value="localhost"></label><br>
        <label>DB Port: <input type="number" name="db_port" value="3306"></label><br>
        <label>DB User: <input type="text" name="db_user"></label><br>
        <label>DB Pass: <input type="password" name="db_pass"></label><br>
        <label>DB Name: <input type="text" name="db_name"></label><br>
        <label>Table Prefix: <input type="text" name="db_prefix" value="maru_"></label><br><br>

        <h3>관리자 계정 설정</h3>
        <label>이름: <input type="text" name="admin_name" required></label><br>
        <label>Password: <input type="password" name="admin_pass" required></label><br><br>

        <button type="submit">설치하기</button>
    </form>
</body>
</html>
