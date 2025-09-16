<?php
if (!isset($this->db)) {
    die("❌ DB 연결이 없습니다.");
}

$board_name = $_GET['board'] ?? 'default';
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id <= 0) {
    echo "<p>❌ 잘못된 접근입니다.</p>";
    exit;
}

// 게시판 정보 가져오기
$stmtBoard = $this->db->prepare("SELECT id, name, skin FROM ".DB_PREFIX."board WHERE table_id = ?");
$stmtBoard->bind_param("s", $board_name);
$stmtBoard->execute();
$board = $stmtBoard->get_result()->fetch_assoc();
$stmtBoard->close();

if (!$board) {
    echo "<p>❌ 존재하지 않는 게시판입니다.</p>";
    exit;
}

// 게시글 불러오기
$sql = "SELECT p.id, p.title, p.content, p.created_at, p.updated_at, 
               u.username
        FROM ".DB_PREFIX."board_post p
        LEFT JOIN ".DB_PREFIX."users u ON p.author_id = u.id
        WHERE p.id = ? AND p.board_id = ?";
$stmt = $this->db->prepare($sql);
$stmt->bind_param("ii", $post_id, $board['id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    echo "<p>❌ 해당 게시글을 찾을 수 없습니다.</p>";
    exit;
}
?>

<article class="post-view">
    <header>
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <p>
            작성자: <?= htmlspecialchars($post['username'] ?? '익명') ?> |
            작성일: <?= htmlspecialchars($post['created_at']) ?>
            <?php if ($post['updated_at'] !== $post['created_at']): ?>
                (수정됨: <?= htmlspecialchars($post['updated_at']) ?>)
            <?php endif; ?>
        </p>
    </header>
    <div class="post-content">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <footer>
        <a href="/board?board=<?= $board_name ?>&view=list">목록</a> |
        <?php if ($post['id'] == $_SESSION['user']['id']): ?>
        <a href="/board?board=<?= $board_name ?>&view=write&id=<?= $post['id'] ?>">수정</a>
        <?php endif; ?>
        <?php if ($post['id'] == $_SESSION['user']['id'] || $_SESSION['user']['role'] >= 3): ?>
        <a href="/board?board=<?= $board_name ?>&view=delete&id=<?= $post['id'] ?>">삭제</a>
        <?php endif; ?>
    </footer>
</article>

<?php
// 댓글 기능 인클루드
$comment_file = __DIR__ . '/comment.php';
if (file_exists($comment_file)) {
    include $comment_file;
} else {
    echo "<p>⚠ 댓글 기능이 아직 준비되지 않았습니다.</p>";
}
?>