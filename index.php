<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_logined_session();

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Members Area</title>
</head>
<body>
[ <a href="./setting.php">Setting</a> ]
[ <a href="./delete.php">Delete</a> ]
[ <a href="./manage.php">Manage</a> ]
[ <a href="./logout.php?token=<?php echo entity(generate_token()); ?>">Logout</a> ]
<h3>Welcome - <?php echo $_SESSION['username']; ?></h3>
</body>
</html>