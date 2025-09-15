<body>
<header>
    <h1>My CMS</h1>
    <nav>
        <a href="/">홈</a>
        <a href="/board">게시판</a>
        <a href="/admin">어드민</a>
    </nav>
        <?php foreach ($boards as $b): ?>
            <li><a href="/board?board=<?= $b['table_id'] ?>"><?= $b['name'] ?></a></li>
        <?php endforeach; ?>

</header>