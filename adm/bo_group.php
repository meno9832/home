<?php
require_once __DIR__ . '/../config.php';
require_once PATH .'/data/dbconfig.php';
if (!defined('IN_ADMIN')) {
    require_once PATH . '/index.php'; 
    exit;
}
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
    DB_USER,
    DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->query("SELECT id, table_id, name, auth_role FROM " . DB_PREFIX ."board_group ORDER BY id ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Board Group 목록</h2>
    <button type="button" id="btn-add-group">그룹 추가</button>
    <form method="post" action= "/adm/detail/save.php">
        <input type="hidden" name="table" value="board_group">
    <table ID="board-group-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Table ID</th>
                <th>Name</th>
                <th>Auth Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows): ?>
                <?php foreach ($rows as $row): ?>
                    <tr data-id="<?= $row['id'] ?>">
                            <td>
                                <?= htmlspecialchars($row['id']) ?>
                                <input type="hidden" name="id[]" value="<?= htmlspecialchars($row['id']) ?>">
                            </td>
                            <td>
                                <input type="text" name="table_id[]" value="<?= htmlspecialchars($row['table_id']) ?>">
                            </td>
                            <td>
                                <input type="text" name="name[]" value="<?= htmlspecialchars($row['name']) ?>">
                            </td>
                            <td>
                                <select name="auth_role[]">
                                    <option value="0" <?= $row['auth_role']==0 ? 'selected' : '' ?>>게스트</option>
                                    <option value="1" <?= $row['auth_role']==1 ? 'selected' : '' ?>>일반</option>
                                    <option value="2" <?= $row['auth_role']==2 ? 'selected' : '' ?>>러너</option>
                                    <option value="3" <?= $row['auth_role']==3 ? 'selected' : '' ?>>관리자</option>
                                    <option value="4" <?= $row['auth_role']==4 ? 'selected' : '' ?>>최고관리자</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn-delete">삭제</button>
                            </td>
                        </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">데이터가 없습니다.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button type="submit" class="btn-save">저장</button>
    </form>
</body>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){

    // 삭제 버튼 클릭 이벤트 (이벤트 위임)
    $('#board-group-table').on('click', '.btn-delete', function(){
        const $tr = $(this).closest('tr');
        const id = $tr.data('id');
        

        if(!id){
            // 신규 행은 DB에 아직 없으므로 그냥 제거
            $tr.remove();
            return;
        }

        if(confirm("정말 삭제하시겠습니까?")){
            console.log(id);
            $.post('/adm/detail/save.php', {action:'delete', table:'board_group', id:id}, function(res){
                if(res.ok){
                    $tr.remove(); // 화면에서 즉시 제거
                } else {
                    alert('삭제 실패: ' + res);
                }
            });
        }
    });

    $('#btn-add-group').click(function(){
        const $tbody = $('#board-group-table tbody');

        // 새로운 빈 row 생성
        const newRow = `
            <tr>
                <td>신규
                    <input type="hidden" name="id[]" value="">
                </td>
                <td><input type="text" name="table_id[]" value=""></td>
                <td><input type="text" name="name[]" value=""></td>
                <td>
                    <select name="auth_role[]">
                        <option value="0">게스트</option>
                        <option value="1">일반</option>
                        <option value="2">러너</option>
                        <option value="3">관리자</option>
                        <option value="4">최고관리자</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn-delete">삭제</button>
                </td>
            </tr>
        `;

        $tbody.append(newRow);
    });
});
</script>
</html>