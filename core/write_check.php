<?php
// load_editor.php
function load_editor($board) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $user_role = $_SESSION['role'] ?? 0;

    // 권한 체크
    if ($user_role < $board['auth_write']) {
        return '<p>❌ 글쓰기 권한이 없습니다.</p>';
    }


}