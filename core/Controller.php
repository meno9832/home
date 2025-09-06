<?php
class Controller {

    private $db;

    public function __construct() {
        session_start(); // 세션 시작
        // DB 연결
        $this->db = new mysqli('localhost', 'root', '', 'homedb');
        if ($this->db->connect_error) {
            die("DB 연결 실패: " . $this->db->connect_error);
        }
    }

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
        $this->authAdmin();
        include __DIR__ . '/../admin/adhead.php';
        include __DIR__ . '/../admin/admain.php';
        include __DIR__ . '/../admin/admenu.php';
    }
    // 로그인 페이지
    public function login() {
        // 이미 로그인 되어 있으면 홈으로
        if (isset($_SESSION['user'])) {
            header('Location: /');
            exit;
        }

        // 로그인 폼 제출 시
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = $this->db->prepare("SELECT id, username, password, role FROM users WHERE username=? LIMIT 1");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ];
                header('Location: /'); // 로그인 성공 → 홈 이동
                exit;
            } else {
                $error = "아이디 또는 비밀번호가 잘못되었습니다.";
            }
        }

        // 로그인 폼 표시
        include __DIR__ . '/../views/pages/login.php';
    }

    // 로그아웃
    public function logout() {
        session_start();       // 세션 시작
        session_unset();       // 세션 변수 제거
        session_destroy();     // 세션 종료
        session_destroy();
        header('Location: /');
        exit;
    }

    // 관리자 권한 체크
    private function authAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo "권한이 없습니다.";
            exit;
        }
    }
}