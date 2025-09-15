<?php
// 게시판 목록 스킨 (list.php)

// $db        → 컨트롤러에서 넘겨준 DB 연결 객체
// $board     → 현재 게시판 정보 (테이블 ID, 이름, skin 등)
// $board_name → URL에서 받은 게시판 아이디

// 안전하게 board_id 가져오기
$board_id = $db->real_escape_string($board['id']);

// 게시글 가져오기
$sql = "SELECT p.id, p.title, p.author_id, p.created_at, u.username
        FROM ".DB_PREFIX."board_post AS p
        LEFT JOIN ".DB_PREFIX."users AS u ON p.author_id = u.id
        WHERE p.board_id = {$board_id} AND p.status = 1
        ORDER BY p.is_notice DESC, p.created_at DESC
        LIMIT 20";

$result = $db->query($sql);
?>

<h2><?= htmlspecialchars($board['name']) ?> 게시판</h2>

<a href="/board?board=<?= $board['table_id'] ?>&view=write" class="btn">글쓰기</a>

<table width="100%" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>제목</th>
            <th>작성자</th>
            <th>작성일</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <a href="?board=<?= urlencode($board_name) ?>&view=detail&id=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['title']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['username'] ?? '익명') ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">게시글이 없습니다.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
