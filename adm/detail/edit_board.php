<?php
// board_edit.php
require_once __DIR__ . '/../../config.php';

$id = $_GET['id'] ?? ($_POST['id'] ?? null);

if (!$id) {
    echo "<p>❌ 잘못된 접근입니다. id가 필요합니다.</p>";
    exit;
}

try {
    $db= new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo "<p>❌ 데이터베이스 연결 오류: " . $e->getMessage() . "</p>";
    exit;
}

// 게시판 정보 불러오기 (ID를 기준으로)
$stmt = $db->prepare("SELECT * FROM " . DB_PREFIX . "board WHERE table_id = :id");
$stmt->execute([':id' => $id]);
$board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$board) {
    echo "<p>❌ 게시판을 찾을 수 없습니다.</p>";
    exit;
}

// 그룹 목록 불러오기
$groups = $db->query("SELECT id, table_id, name FROM " . DB_PREFIX . "board_group ORDER BY id ASC")
              ->fetchAll(PDO::FETCH_ASSOC);

// 업데이트 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // id와 table_id는 업데이트 대상에서 제외합니다.
    $fields = [
        'name','group_id','category','use_category','skin','password',
        'auth_list','auth_read','auth_write','auth_comment','modify_level','delete_level','secret_level','use_noname',
        'file_count','file_size','use_html_editor','txt_min','txt_max','comment_min','comment_max',
        'content_top','insert_content',
        'page_row','image_width','new_icon_hour','reply_order','list_order',
        'read_point','write_point','comment_point'
    ];

    $updateData = [];
    $params = [];
    
    foreach ($fields as $field) {
        $updateData[] = "$field = :$field";
        $value = $_POST[$field] ?? null;

        // 체크박스 값 처리: 'on'이면 1, 아니면 0으로 변환
        if (in_array($field, ['use_category', 'use_html_editor', 'use_noname', 'auth_list', 'auth_read', 'auth_write', 'auth_comment', 'secret_level', 'reply_order', 'list_order'])) {
            $params[":$field"] = ($value === 'on') ? 1 : 0;
        // 숫자형 값 처리: 숫자가 아니면 0으로 변환
        } elseif (in_array($field, ['modify_level', 'delete_level', 'file_count', 'file_size', 'txt_min', 'txt_max', 'comment_min', 'comment_max', 'page_row', 'image_width', 'new_icon_hour', 'read_point', 'write_point', 'comment_point'])) {
            $params[":$field"] = is_numeric($value) ? (int)$value : 0;
        } else {
            // 그 외의 문자열 필드는 그대로 사용
            $params[":$field"] = $value;
        }
    }
    
    // WHERE 절에 사용할 id 파라미터를 별도로 추가합니다.
    $params[':id'] = $id;

    $sql = "UPDATE " . DB_PREFIX . "board SET " . implode(', ', $updateData) . " WHERE id = :id";
    $stmt = $db->prepare($sql);

    // 디버깅을 위한 출력 (실제 운영 시에는 제거해야 합니다)
    // echo "SQL 쿼리: " . $sql . "<br>";
    // echo "파라미터: ";
    // print_r($params);
    // exit;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 이 줄을 추가하여 POST 데이터 확인
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit;
    }

    if ($stmt->execute($params)) {
        echo "<p>✅ 게시판이 성공적으로 수정되었습니다.</p>";
        echo "<a href='/adm?page=board'>목록으로 돌아가기</a>";
        exit;
    } else {
        echo "<p>❌ 수정 중 오류가 발생했습니다.</p>";
        exit;
    }
}
?>

<h2>게시판 수정: <?= htmlspecialchars($board['name'] ?? '') ?></h2>
<form method="post">
    <!-- id는 고유 식별자이므로 수정되지 않게 숨김 처리 -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($board['id'] ?? '') ?>">
    <input type="hidden" name="password" value="<?= htmlspecialchars($board['password'] ?? '') ?>">

    <label>테이블 ID: <input type="text" name="table_id" value="<?= htmlspecialchars($board['table_id'] ?? '') ?>"></label><br>
    <label>게시판 이름: <input type="text" name="name" value="<?= htmlspecialchars($board['name'] ?? '') ?>"></label><br>
    <label>그룹 ID:
        <select name="group_id">
            <?php foreach ($groups as $g): ?>
                <option value="<?= htmlspecialchars($g['table_id'] ?? '') ?>"
                    <?= ($board['group_id'] ?? null) === $g['table_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['name'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br>
    <label>스킨: <input type="text" name="skin" value="<?= htmlspecialchars($board['skin'] ?? '') ?>"></label><br>
    <label>카테고리 사용: <input type="checkbox" name="use_category" <?= ($board['use_category'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>카테고리 목록: <input type="text" name="category" value="<?= htmlspecialchars($board['category'] ?? '') ?>"></label><br>
    <label>권한 - 리스트 보기: <input type="checkbox" name="auth_list" <?= ($board['auth_list'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>권한 - 읽기: <input type="checkbox" name="auth_read" <?= ($board['auth_read'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>권한 - 작성: <input type="checkbox" name="auth_write" <?= ($board['auth_write'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>권한 - 댓글: <input type="checkbox" name="auth_comment" <?= ($board['auth_comment'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>수정 레벨: <input type="number" name="modify_level" value="<?= htmlspecialchars($board['modify_level'] ?? '') ?>"></label><br>
    <label>삭제 레벨: <input type="number" name="delete_level" value="<?= htmlspecialchars($board['delete_level'] ?? '') ?>"></label><br>
    <label>비밀글 사용: <input type="checkbox" name="secret_level" <?= ($board['secret_level'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>익명 사용: <input type="checkbox" name="use_noname" <?= ($board['use_noname'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>첨부 파일 수: <input type="number" name="file_count" value="<?= htmlspecialchars($board['file_count'] ?? '') ?>"></label><br>
    <label>파일 최대 용량(KB): <input type="number" name="file_size" value="<?= htmlspecialchars($board['file_size'] ?? '') ?>"></label><br>
    <label>HTML 에디터 사용: <input type="checkbox" name="use_html_editor" <?= ($board['use_html_editor'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>본문 최소 글자: <input type="number" name="txt_min" value="<?= htmlspecialchars($board['txt_min'] ?? '') ?>"></label><br>
    <label>본문 최대 글자: <input type="number" name="txt_max" value="<?= htmlspecialchars($board['txt_max'] ?? '') ?>"></label><br>
    <label>댓글 최소 글자: <input type="number" name="comment_min" value="<?= htmlspecialchars($board['comment_min'] ?? '') ?>"></label><br>
    <label>댓글 최대 글자: <input type="number" name="comment_max" value="<?= htmlspecialchars($board['comment_max'] ?? '') ?>"></label><br>
    <label>페이지당 글 수: <input type="number" name="page_row" value="<?= htmlspecialchars($board['page_row'] ?? '') ?>"></label><br>
    <label>이미지 폭: <input type="number" name="image_width" value="<?= htmlspecialchars($board['image_width'] ?? '') ?>"></label><br>
    <label>신규 아이콘 시간(h): <input type="number" name="new_icon_hour" value="<?= htmlspecialchars($board['new_icon_hour'] ?? '') ?>"></label><br>
    <label>리스트 정렬 방식: <input type="checkbox" name="list_order" <?= ($board['list_order'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>답글 정렬 방식: <input type="checkbox" name="reply_order" <?= ($board['reply_order'] ?? false) ? 'checked' : '' ?>></label><br>
    <label>읽기 포인트: <input type="number" name="read_point" value="<?= htmlspecialchars($board['read_point'] ?? '') ?>"></label><br>
    <label>작성 포인트: <input type="number" name="write_point" value="<?= htmlspecialchars($board['write_point'] ?? '') ?>"></label><br>
    <label>댓글 포인트: <input type="number" name="comment_point" value="<?= htmlspecialchars($board['comment_point'] ?? '') ?>"></label><br>
    <label>상단 내용:</label><br>
    <textarea name="content_top" rows="4" style="width:100%;"><?= htmlspecialchars($board['content_top'] ?? '') ?></textarea><br>
    <label>본문 삽입 내용:</label><br>
    <textarea name="insert_content" rows="4" style="width:100%;"><?= htmlspecialchars($board['insert_content'] ?? '') ?></textarea><br>
    <button type="submit">저장</button>
</form>
