<?php
class Controller {

    // 홈페이지 메인
    public function index() {
        include __DIR__ ."/../main.php";
    }

    // 게시판 처리
    public function board() {
        $board_name = $_GET['board'] ?? 'default';
        $view = $_GET['view'] ?? 'list';

        // DB 연결 예시는 나중에 추가 가능
        // $conn = new mysqli(...);

        if ($view === 'list') {
            echo "<h2>게시판 목록: {$board_name}</h2>";
            echo "<ul>
                    <li>예시 게시물 1</li>
                    <li>예시 게시물 2</li>
                  </ul>";
        } elseif ($view === 'detail') {
            $id = $_GET['id'] ?? 0;
            echo "<h2>게시글 상세: {$board_name} / ID: {$id}</h2>";
            echo "<p>게시글 내용 예시</p>";
        } else {
            echo "<p>존재하지 않는 뷰: {$view}</p>";
        }
    }
    public function admin() {
        // 세션 시작
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 권한 체크
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header("Location: /login");
            exit;
        }
        include __DIR__ . '/../admin/admain.php';
    }
}