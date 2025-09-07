<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $layout = $_POST["layout_json"] ?? "[]";
    $file = __DIR__ . "/layout.json";
    if (file_put_contents($file, $layout) !== false) {
        echo json_encode(["status"=>"success"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"파일 저장 실패"]);
    }
} else {
    echo json_encode(["status"=>"error","message"=>"잘못된 요청"]);
}
