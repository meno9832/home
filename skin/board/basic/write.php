<?php
// write.php - 게시글 작성 스킨
// $db     : mysqli 연결 객체
// $board  : 현재 게시판 정보
// $board_name : 게시판 table_id
require_once PATH . '/core/write_check.php';
$editor_html = load_editor($board);
?>

<h2><?= htmlspecialchars($board['name']) ?> 게시판 글쓰기</h2>

<?php
// 글 저장 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $user_id = $_SESSION['user_id'] ?? 0; // 로그인 사용자
    $board_id = $board['id'];

    if ($title && $content) {
        $stmt = $db->prepare("INSERT INTO ".DB_PREFIX."board_post (board_id, author_id, title, content, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $board_id, $user_id, $title, $content);
        if ($stmt->execute()) {
            echo "<p>✅ 글이 등록되었습니다.</p>";
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
        <input type="text" id="title" name="title" required style="width:100%">
    </div>
    <div>
        <label for="content">내용</label><br>
        <?php echo $editor_html; ?>
    </div>
    <div>
        <button type="submit">글 작성</button>
        <a href="/board?board=<?= $board['table_id'] ?>&view=list">취소</a>
    </div>
</form>
