<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_logined_session();

// 初期化
$result = "";

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー情報変更
    $result = user_conf_change(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS), $_SESSION['id']);

    if( $result ) {
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        // ユーザ名をセット
        $_SESSION['username'] = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
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
[ <a href="./index.php">Return</a> ]
[ <a href="./logout.php?token=<?php echo entity(generate_token()); ?>">Logout</a> ]
<h3>Change Stting?</h3>
<? echo $_SESSION['username']; ?>
<form method="post" action="<?php echo basename(__FILE__); ?>">
    Login ID: <input type="text" name="user_id" value="<?php echo $_SESSION['user_id']; ?>" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="Login ID" required><br />
    UserName: <input type="text" name="user_name" value="<?php echo $_SESSION['username']; ?>" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="User ID" required><br />
    Password: <input type="password" name="pw" value="" minlength="<?php echo LENGTH_PW_MIN;?>" maxlength="<?php echo LENGTH_PW_MAX;?>" placeholder="Password" required><br />
    <input type="submit" value="Change">
</form>
<?php if ( $result === true ): ?>
<p style="color: red;">変更しました</p>
<?php endif; ?>
<?php if ( $result === false): ?>
<p style="color: red;">変更できませんでした。入力内容を確認してください</p>
<?php endif; ?>
</body>
</html>