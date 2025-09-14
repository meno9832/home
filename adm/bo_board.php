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

// board_group 목록 불러오기
$stmt = $pdo->query("SELECT id, name FROM " . DB_PREFIX . "board_group");
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
// board 목록 불러오기
$stmt = $pdo->query("SELECT id, table_id, name, group_id FROM " . DB_PREFIX ."board");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$skinDir = PATH . "/skin/board";
$folders = array_filter(glob($skinDir . '/*'), 'is_dir');
$folderNames = array_map('basename', $folders);
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
    <h2 style="text-align:center;">Board 목록</h2>
    <button type="button" id="btn-add-board">게시판 추가</button>
    <form method="post" action= "/adm/detail/save.php">
        <input type="hidden" name="table" value="board">
        <table id="board-table">
            <thead>
                <tr>
                    <th>Table ID</th>
                    <th>Name</th>
                    <th>Group</th>
                    <th>Skin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows): ?>
                    <?php foreach ($rows as $row): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td>
                                <?= htmlspecialchars($row['table_id']) ?>
                                <input type="hidden" name="id[]" value="<?= htmlspecialchars($row['id']) ?>">
                            </td>
                            <td>
                                <input type="text" name="name[]" value="<?= htmlspecialchars($row['name']) ?>">
                            </td>
                            <td>
                                <select name="group_id[]">
                                    <?php foreach ($groups as $group): ?>
                                        <option value="<?= $group['id'] ?>" <?= $row['group_id'] == $group['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($group['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="skin[]">
                                    <?php foreach ($folderNames as $folder): ?>
                                        <option value="<?= $folder ?>" <?= ($row['folder_name'] ?? '') === $folder ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($folder) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn-mod">수정</button>
                                <button type="button" class="btn-delete">삭제</button>
                            </td>
                        </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">데이터가 없습니다.</td>
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
    $('#board-table').on('click', '.btn-delete', function(){
        const $tr = $(this).closest('tr');
        const id = $tr.data('id');
        

        if(!id){
            // 신규 행은 DB에 아직 없으므로 그냥 제거
            $tr.remove();
            return;
        }

        if(confirm("정말 삭제하시겠습니까?")){
            console.log(id);
            $.post('/adm/detail/save.php', {action:'delete', table:'board', id:id}, function(res){
                if(res.ok){
                    $tr.remove(); // 화면에서 즉시 제거
                } else {
                    alert('삭제 실패: ' + res);
                }
            });
        }
    });

    $('#btn-add-board').click(function(){
        const $tbody = $('#board-table tbody');

        // PHP에서 미리 select 옵션 문자열을 만들어두면 깔끔함
        const groupOptions = `<?php foreach ($groups as $group): ?>
            <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
        <?php endforeach; ?>`;

        const skinOptions = `<?php foreach ($folderNames as $folder): ?>
            <option value="<?= $folder ?>"><?= htmlspecialchars($folder) ?></option>
        <?php endforeach; ?>`;

        // 새로운 빈 row 생성
        const newRow = `
            <tr>
                <td>
                    <input type="text" name="table_id[]" value="">
                    <input type="hidden" name="id[]" value="">
                </td>
                <td>
                    <input type="text" name="name[]" value="">
                </td>
                <td>
                    <select name="group_id[]">
                        ${groupOptions}
                    </select>
                </td>
                <td>
                    <select name="skin[]">
                        ${skinOptions}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn-mod">수정</button>
                    <button type="button" class="btn-delete">삭제</button>
                </td>
            </tr>
        `;
        $tbody.append(newRow);
    });
});
</script>
</html>