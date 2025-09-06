<?php
class Router {
    protected $routes = [];

    public function __construct() {
        // URL 경로 → 컨트롤러/메서드 매핑
        $this->routes = [
            '/'         => 'index',
            '/board'    => 'board',
            '/admin'    => 'admin',
            '/login'    => 'login',
            '/logout'   => 'logout',
        ];
    }

    public function dispatch() {
        // 현재 요청 URL 경로 가져오기
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');  // 끝 슬래시 제거
        if ($uri === '') $uri = '/';  // 루트 경로 처리

        echo "<pre>URI: "; var_dump($uri); echo "</pre>";

        if (isset($this->routes[$uri])) {
            $method = $this->routes[$uri];

            // 컨트롤러 파일은 core 폴더 하나
            $controllerFile = __DIR__ . '/Controller.php';

            if (!file_exists($controllerFile)) {
                die("Controller 파일 없음: $controllerFile");
            }

            require_once $controllerFile;

            if (!class_exists('Controller')) {
                die("Controller 클래스 없음");
            }

            $controller = new Controller();

            if (!method_exists($controller, $method)) {
                die("메서드 없음: $method");
            }

            // 컨트롤러 메서드 실행
            $controller -> $method();

        } else {
            // 라우트가 없으면 404
            http_response_code(404);
            echo "<h1>404 Not Found</h1>";
            echo "<p>요청하신 페이지를</p>";
        }
    }
}
