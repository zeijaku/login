<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_logined_session();

// 初期化
$result = "";
$error = "";

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {

    if( filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS) == filter_input(INPUT_POST, 'repw', FILTER_SANITIZE_SPECIAL_CHARS) ) {
        // ユーザー情報変更
        $result = user_delete(filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS), $_SESSION['id']);
        
        if( $result ) {
            // セッション用Cookieの破棄
            setcookie(session_name(), '', 1);
            // セッションファイルの破棄
            session_destroy();
            // ログアウト完了後に /login.php に遷移
            header('Location: ./login.php');
        } else {
            // 削除が失敗したとき
            $error = "削除出来ませんでした。入力内容を再確認してください";
        }
    } else {
        $error = "入力パスワードが一致していません";
    }
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Members Area</title>
</head>
<body>
[ <a href="/login/index.php">Return</a> ]
[ <a href="/login/logout.php?token=<?php echo entity(generate_token()); ?>">Logout</a> ]
<h3>User Delete?</h3>
<?php echo $_SESSION['username']; ?>
<form method="post" action="<?php echo basename(__FILE__); ?>">
    Password: <input type="password" name="pw" value="">
    Re Password: <input type="password" name="repw" value="">
    <input type="hidden" name="token" value="<?php echo entity(generate_token()); ?>">
    <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
    <input type="submit" value="Delete!!">
</form>
<?php if ( $error ): ?>
<p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
</body>
</html>