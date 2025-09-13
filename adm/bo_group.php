<?php
require_once PATH .'/data/dbconfig.php';
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
    <form method="post" action= "/adm/detail/save.php">
        <input type="hidden" name="table" value="board_group">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Table ID</th>
                <th>Name</th>
                <th>Auth Role</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows): ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
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
</html>