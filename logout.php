<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

require_logined_session();

// CSRFトークンを検証
if (!validate_token(filter_input(INPUT_GET, 'token'))) {
    // 「400 Bad Request」
    header('Content-Type: text/plain; charset=UTF-8', true, 400);
    exit('トークンが無効です');
}
//セッションクッキーの削除
if (isset($_COOKIE["PHPSESSID"])) {
    setcookie("PHPSESSID", '', time() - 1800, '/');
}
// セッションファイルの破棄
session_destroy();

echo "[ <a href='./login.php'>Login</a> ]";
echo "<h3>ログアウトしました</h3>";
