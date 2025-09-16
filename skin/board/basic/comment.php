<?php
// 현재 로그인 사용자
$current_user_id = $_SESSION['user_id'] ?? null;

// ---------------------------------
// 댓글 저장 처리 (작성)
// ---------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $post_id = intval($_POST['post_id']);
    $content = trim($_POST['content']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    $current_user_id = $_SESSION['user']['id'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
        if (!$current_user_id) {
            die("로그인 후 댓글을 작성할 수 있습니다.");
        }
    }

    if ($post_id > 0 && $content !== '') {
        $stmt = $this->db->prepare("INSERT INTO ".DB_PREFIX."board_comment 
            (post_id, user_id, is_anonymous, content, created_at) 
            VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiis", $post_id, $current_user_id, $is_anonymous, $content);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ---------------------------------
// 댓글 수정 처리
// ---------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_update'])) {
    $comment_id = intval($_POST['comment_id']);
    $content = trim($_POST['content']);

    if ($comment_id > 0 && $content !== '') {
        // 본인 댓글인지 확인
        $check = $this->db->prepare("SELECT user_id FROM ".DB_PREFIX."board_comment WHERE id=?");
        $check->bind_param("i", $comment_id);
        $check->execute();
        $check->bind_result($owner_id);
        $check->fetch();
        $check->close();

        if ($owner_id == $current_user_id) {
            $stmt = $this->db->prepare("UPDATE ".DB_PREFIX."board_comment SET content=? WHERE id=?");
            $stmt->bind_param("si", $content, $comment_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ---------------------------------
// 댓글 삭제 처리
// ---------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_delete'])) {
    $comment_id = intval($_POST['comment_id']);

    if ($comment_id > 0) {
        // 본인 댓글인지 확인
        $check = $this->db->prepare("SELECT user_id FROM ".DB_PREFIX."board_comment WHERE id=?");
        $check->bind_param("i", $comment_id);
        $check->execute();
        $check->bind_result($owner_id);
        $check->fetch();
        $check->close();

        if ($owner_id == $current_user_id) {
            $stmt = $this->db->prepare("DELETE FROM ".DB_PREFIX."board_comment WHERE id=?");
            $stmt->bind_param("i", $comment_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// ---------------------------------
// 댓글 목록 불러오기
// ---------------------------------
$stmt = $this->db->prepare("
    SELECT c.id, c.content, c.created_at, c.is_anonymous, c.user_id, u.username
    FROM ".DB_PREFIX."board_comment c
    LEFT JOIN ".DB_PREFIX."users u ON c.user_id = u.id
    WHERE c.post_id = ?
    ORDER BY c.created_at ASC
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<section class="comments">
    <h3>댓글</h3>

    <?php if (count($comments) > 0): ?>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li style="margin-bottom:10px; border-bottom:1px solid #ddd; padding:5px 0;">
                    <strong>
                        <?= $comment['is_anonymous'] ? '익명' : htmlspecialchars($comment['username'] ?? '탈퇴회원') ?>
                    </strong>
                    <span style="font-size:0.9em; color:#666;">
                        (<?= htmlspecialchars($comment['created_at']) ?>)
                    </span>
                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>

                    <?php if ($comment['user_id'] == $current_user_id): ?>
                        <!-- 수정 버튼 -->
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <textarea name="content" rows="2" style="width:100%"><?= htmlspecialchars($comment['content']) ?></textarea><br>
                            <button type="submit" name="comment_update">수정</button>
                        </form>

                        <!-- 삭제 버튼 -->
                        <form method="post" action="" style="display:inline;" 
                              onsubmit="return confirm('정말 삭제하시겠습니까?');">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <button type="submit" name="comment_delete">삭제</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>아직 댓글이 없습니다.</p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="post_id" value="<?= $post_id ?>">
        <textarea name="content" rows="4" style="width:100%" required></textarea><br>
        <label>
            <input type="checkbox" name="is_anonymous" value="1"> 익명으로 작성
        </label><br>
        <button type="submit" name="comment_submit">댓글 작성</button>
    </form>
</section>
