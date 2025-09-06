<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원가입</title>
</head>
<body>
    <h1>회원가입</h1>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" action="/register">
        <label>이름: <input type="text" name="username" required></label><br>
        <label>비밀번호: <input type="password" name="password" required></label><br>
        <label>비밀번호 확인: <input type="password" name="password_confirm" required></label><br>
        <button type="submit">회원가입</button>
    </form>
    <p>이미 계정이 있다면 <a href="/login">로그인</a></p>
</body>
</html>
