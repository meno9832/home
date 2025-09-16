<?php
// write.php - 게시글 작성 스킨
// $db     : mysqli 연결 객체
// $board  : 현재 게시판 정보
// $board_name : 게시판 table_id
require_once PATH . '/core/write_check.php';
$editor_html = load_editor($board);

$board_name = $_GET['board'] ?? 'default';
$post_id = $_GET['id'] ?? null;

$title = '';
$content = '';

// ✅ 수정 모드일 경우 게시글 데이터 가져오기
if ($post_id) {
    $stmt = $this->db->prepare("
        SELECT title, content 
        FROM " . DB_PREFIX . "board_post 
        WHERE id = ? AND board_id = (
            SELECT id FROM " . DB_PREFIX . "board WHERE table_id = ?
        ) LIMIT 1
    ");
    $stmt->bind_param("is", $post_id, $board_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $title = htmlspecialchars($row['title'], ENT_QUOTES);
        $content = htmlspecialchars($row['content'], ENT_QUOTES);
    }
}
?>


<h2><?= htmlspecialchars($board['name']) ?> 게시판 글쓰기</h2>

<?php
// 글 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user']['id'] ?? 0; // 로그인 사용자
    $board_id = $board['id'];

    if ($title && $content) {
        if ($post_id) {
            // 수정 (UPDATE)
            $stmt = $db->prepare("
                UPDATE " . DB_PREFIX . "board_post
                SET title=?, content=?
                WHERE id=? AND author_id=? AND board_id=?
            ");
            $stmt->bind_param("siiii", $title, $content, $post_id, $user_id, $board_id);
            $action = "수정";
        } else {
            // 새 글 작성 (INSERT)
            $stmt = $db->prepare("
                INSERT INTO " . DB_PREFIX . "board_post
                (board_id, author_id, title, content, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("iiss", $board_id, $user_id, $title, $content);
            $action = "등록";
        }

        if ($stmt->execute()) {
            echo "<p>✅ 글이 {$action}되었습니다.</p>";
            echo "<a href=\"/board?board=" . $board['table_id'] . "&view=list\">목록으로 돌아가기</a>";
            $stmt->close();
            return;
        } else {
            echo "<p>❌ 글 저장 중 오류가 발생했습니다.</p>";
        }
    } else {
        echo "<p>❌ 제목과 내용을 모두 입력해주세요.</p>";
    }
}
?>

<form method="post" action="">
    <div>
        <label for="title">제목</label><br>
        <input type="text" id="title" name="title" value="<?= $title ?>" required style="width:100%">
    </div>
    <div>
        <label for="content">내용</label><br>
            <textarea id="content" name="content" value="<?= $content ?>" rows="10" style="width:100%"><?= $content ?></textarea>
    </div>
    <div>
        <button type="submit">글 작성</button>
        <a href="/board?board=<?= $board['table_id'] ?>&view=list">취소</a>
    </div>
</form>
