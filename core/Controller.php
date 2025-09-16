<?php
class Controller {

    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // DB 연결
        $this->db = new mysqli(DB_HOST, DB_USER,  DB_PASS, DB_NAME,DB_PORT);
        if ($this->db->connect_error) {
            die("DB 연결 실패: " . $this->db->connect_error);
        }
        $this->db->set_charset("utf8mb4");
    }

    // 홈페이지 메인
    public function index() {
        $result = $this->db->query("SELECT * FROM ".DB_PREFIX."board");
        $boards = $result->fetch_all(MYSQLI_ASSOC);
        include PATH ."/common/head.php";
        include PATH ."/main.php";
        include PATH ."/common/footer.php";
    }

    // 게시판 처리
    public function board() {
        $board_id = $_GET['board'] ?? 'default';
        $view = $_GET['view'] ?? 'list';
        
        $db = $this->db;

        // DB 연결 (컨트롤러에 이미 $this->db 있다고 가정)
        $stmt = $this->db->prepare("SELECT * FROM ".DB_PREFIX."board WHERE table_id = ?");
        $stmt->bind_param("s", $board_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $board = $result->fetch_assoc();

        if (!$board) {
            echo "<p>존재하지 않는 게시판입니다.</p>";
            return;
        }

        $skin = $board['skin']; // 스킨이 없으면 기본값 사용
        $skin_path = PATH_SKIN . "/{$skin}";
        
        if ($view === 'list') {
            include $skin_path . "/list.php";
        } elseif ($view === 'view') {
            $id = $_GET['id'] ?? 0;
            include $skin_path . "/view.php";
        } elseif ($view === 'write') {
            include $skin_path . "/write.php";
        } elseif ($view === "delete") {
            if ($_GET['id']) {
            $stmt = $db->prepare("
                DELETE FROM " . DB_PREFIX . "board_post
                WHERE id=?
            ");
            $stmt->bind_param("i", $_GET['id']);
            if ($stmt->execute()) {
                echo "<p>✅ 글이 삭제되었습니다.</p>";
                echo "<a href=\"/board?board=" . $board['table_id'] . "&view=list\">목록으로 돌아가기</a>";
                exit;
            } else {
                echo "<p>❌ 글 삭제 중 오류가 발생했습니다.</p>";
            }
            } else {
                echo "<p>❌ 삭제할 글 ID가 지정되지 않았습니다.</p>";
            }
        }else {
            echo "<p>존재하지 않는 뷰: {$view}</p>";
        }
    }
    public function admin() {
        $this->authAdmin();
        define('IN_ADMIN', true);
        include PATH . '/adm/adhead.php'; ?>
        <div class="container">
            <?php
            include PATH . '/adm/admenu.php';
            include PATH . '/adm/admain.php';
            ?>
        </div>
        <?php
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

            $stmt = $this->db->prepare(
                "SELECT id, username, password, role 
                FROM " . DB_PREFIX . "users 
                WHERE username=? LIMIT 1");
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
        include PATH . '/skin/session/login.php';
    }

    // 로그아웃
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }       // 세션 시작
        session_unset();       // 세션 변수 제거
        session_destroy();  
        header('Location: /');
        exit;
    }

    // 관리자 권한 체크
    private function authAdmin() {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 로그인 여부 확인
        if (!isset($_SESSION['user'])) {
            // 로그인 안 되어 있으면 로그인 페이지로
            header('HTTP/1.1 302 Found');
            header('Location: /login');
            exit;
        }

        // 관리자 권한 확인
        if ($_SESSION['user']['role'] < 3) {
            // 관리자 아님 → 얼럿 후 홈으로 이동
            echo "<script>alert('권한이 없습니다.'); window.location.href = '/';</script>";
            exit;
        }
    }
    public function register() {
    // 이미 로그인 되어있으면 홈으로 이동
    if (isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }

    $error = '';

    // 회원가입 폼 제출 시
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // 간단한 유효성 검사
        if (empty($username) || empty($password)) {
            $error = "이름과 비밀번호를 입력해주세요.";
        } elseif ($password !== $password_confirm) {
            $error = "비밀번호가 일치하지 않습니다.";
        } else {
            // 중복 확인
            $stmt = $this->db->prepare("SELECT id FROM " . DB_PREFIX . "users WHERE username=? LIMIT 1");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "이미 존재하는 이름입니다.";
            } else {
                // 새 사용자 등록
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 1; // 기본 권한
                $stmt = $this->db->prepare("INSERT INTO " . DB_PREFIX . "users (username, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param('sss', $username, $hash, $role);
                if ($stmt->execute()) {
                    // 가입 성공 → 로그인 페이지로 이동
                    header('Location: /login');
                    exit;
                } else {
                    $error = "회원가입 실패. 다시 시도해주세요.";
                }
            }
        }
    }

    // 회원가입 폼 표시
    include PATH . '/skin/session/register.php';
}
}