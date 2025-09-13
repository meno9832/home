<?php
require_once PATH .'/data/dbconfig.php';

if (!defined('IN_ADMIN')) {
    require_once PATH . '/index.php'; 
    exit;
}
$uploadDir = PATH. "/data/system_file/";     // 실제 저장 경로
$uploadUrl = "/data/system_file/";  

// 업로드 처리 함수
function handle_upload(array $file, string $prefix, string $uploadDir, string $uploadUrl, array $allowedExt = ['png','jpg','jpeg','gif','ico','svg'], int $maxSize = 5242880) {
    // $maxSize 기본 5MB
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [ 'success' => false, 'reason' => 'no_file' ];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [ 'success' => false, 'reason' => 'php_error', 'code' => $file['error'] ];
    }

    if ($file['size'] > $maxSize) {
        return [ 'success' => false, 'reason' => 'too_large' ];
    }

    // MIME/type 검사 (더 안전)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // 허용되는 mime 대역을 간단 검사 (이미지 위주)
    $allowedMimes = ['image/png','image/jpeg','image/gif','image/x-icon','image/vnd.microsoft.icon','image/svg+xml'];
    if (!in_array($mime, $allowedMimes)) {
        // 일부 환경에서는 ico의 mime이 다르게 나올 수 있으니 확장자 검사도 함께
        // 계속 진행해서 확장자 검사로 판정
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return [ 'success' => false, 'reason' => 'bad_ext', 'ext' => $ext ];
    }

    // 파일명 생성 (충돌 방지)
    try {
        $rand = bin2hex(random_bytes(6));
    } catch(Exception $e) {
        $rand = mt_rand(1000,9999);
    }
    $newName = $prefix . '_' . time() . '.' . $ext;
    $target = $uploadDir . $newName;

    // 실제 업로드 (move_uploaded_file)
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return [ 'success' => false, 'reason' => 'move_failed' ];
    }

    // 퍼미션 설정 (권장)
    @chmod($target, 0644);

    // 반환: 웹에서 접근 가능한 경로
    return [ 'success' => true, 'path' => $uploadUrl . $newName, 'fullpath' => $target ];
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
$conn->set_charset(DB_CHARSET);

$message = "";

// POST 요청일 경우 → 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $faviconPath = $_POST['favicon_existing'] ?? null;
    $mainImagePath = $_POST['main_image_existing'] ?? null;
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $allow_account_create   = isset($_POST['allow_account_create']) ? 1 : 0;
    $allow_character_create = isset($_POST['allow_character_create']) ? 1 : 0;
    $allow_character_edit   = isset($_POST['allow_character_edit']) ? 1 : 0;


    // 🟢 파비콘 업로드
    if (isset($_FILES['favicon'])) {
        $res = handle_upload($_FILES['favicon'], 'favicon', $uploadDir, $uploadUrl);
        if ($res['success']) {
            $faviconPath = URL_PATH . '/data/system_file/' . basename($res['path']);
        } else {
            // 디버그용: 에러 표시 (운영 환경에서는 로그에만 남길 것)
            // possible reasons: no_file, php_error, too_large, bad_ext, move_failed...
            $uploadError = $res;
            // 예: $message = "파비콘 업로드 실패: " . json_encode($uploadError);
        }
    }

    // 🟢 대표 이미지 업로드
    if (isset($_FILES['main_image'])) {
        $res = handle_upload($_FILES['main_image'], 'main_image', $uploadDir, $uploadUrl);
        if ($res['success']) {
            $mainImagePath = URL_PATH . '/data/system_file/' . basename($res['path']);
        } else {
            // 디버그용: 에러 표시 (운영 환경에서는 로그에만 남길 것)
            $uploadError = $res;
            // 예: $message = "대표 이미지 업로드 실패: " . json_encode($uploadError);
        }
    }

    $stmt = $conn->prepare("
        UPDATE " . DB_PREFIX . "site_setting
        SET is_public=?, site_title=?, site_description=?, favicon=?, main_image=?, bgm=?, twitter_widget=?,
        allow_account_create=?, allow_character_create=?, allow_character_edit=?
        WHERE id=1
    ");
    $stmt->bind_param(
        "issssssiii",
        $is_public,
        $_POST['site_title'],
        $_POST['site_description'],
        $faviconPath,
        $mainImagePath,
        $_POST['bgm'],
        $_POST['twitter_widget'],
        $allow_account_create,
        $allow_character_create,
        $allow_character_edit
    );

    if ($stmt->execute()) {
        $message = "✅ 저장 성공";
    } else {
        $message = "❌ 저장 실패: " . $stmt->error;
    }

    $stmt->close();
}

// 현재 설정 불러오기
$sql = "SELECT * FROM " . DB_PREFIX . "site_setting WHERE id=1 LIMIT 1";
$result = $conn->query($sql);
$settings = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>사이트 설정</title>
    <style>
        body { font-family: Arial, sans-serif; margin:20px; }
        .form-group { margin-bottom: 15px; }
        label { display:block; font-weight:bold; margin-bottom:5px; }
        input[type="text"], textarea, select {
            width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;
        }
        button { padding:10px 20px; background:#007bff; color:white; border:none; border-radius:5px; cursor:pointer; }
        button:hover { background:#0056b3; }
    </style>
</head>
<body>
    <h1>사이트 기본 설정</h1>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_public" value="1" 
                    <?= $settings['is_public'] ? 'checked' : '' ?>>
                사이트 공개
            </label>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="allow_account_create" value="1" 
                    <?= $settings['allow_account_create'] ? 'checked' : '' ?>>
                계정 생성 가능
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="allow_character_create" value="1" 
                    <?= $settings['allow_character_create'] ? 'checked' : '' ?>>
                캐릭터 생성 가능
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="allow_character_edit" value="1" 
                    <?= $settings['allow_character_edit'] ? 'checked' : '' ?>>
                캐릭터 수정 가능
            </label>
        </div>

        <div class="form-group">
            <label>홈페이지 제목</label>
            <input type="text" name="site_title" value="<?= htmlspecialchars($settings['site_title']) ?>">
        </div>

        <div class="form-group">
            <label>사이트 설명</label>
            <textarea name="site_description"><?= htmlspecialchars($settings['site_description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>파비콘 업로드</label>
            <input type="file" name="favicon">
            <?php if ($settings['favicon']): ?>
                <img src="<?= htmlspecialchars($settings['favicon']) ?>" class="preview">
            <?php endif; ?>
            <input type="text" name="favicon_existing" value="<?= htmlspecialchars($settings['favicon']) ?>">
        </div>

        <div class="form-group">
            <label>대표 이미지 업로드</label>
            <input type="file" name="main_image">
            <?php if ($settings['main_image']): ?>
                <img src="<?= htmlspecialchars($settings['main_image']) ?>" class="preview">
            <?php endif; ?>
            <input type="text" name="main_image_existing" value="<?= htmlspecialchars($settings['main_image']) ?>">
        </div>

        <div class="form-group">
            <label>배경음악 경로/URL</label>
            <input type="text" name="bgm" value="<?= htmlspecialchars($settings['bgm']) ?>">
        </div>

        <div class="form-group">
            <label>트위터 위젯 코드</label>
            <input type="text" name="twitter_widget" value="<?= htmlspecialchars($settings['twitter_widget']) ?>">
        </div>

        <button type="submit">저장</button>
    </form>
</body>
</html>
