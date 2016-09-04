<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// SQLite DB作成
if( DB_TYPE == 'sqlite' ) {
    // SQLite DB作成
    create_userDB();
}

// ログインチェック
require_unlogined_session();

// 初期化
$result = true;

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー検索
    $result = login(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS), filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS));

    if( isset($result['user_id']) ) {
        // 認証が成功したとき
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        // ユーザ名をセット
        $_SESSION['username'] = $result['user_name'];
        $_SESSION['id'] = $result['id'];

        // ログイン完了後に / に遷移
        header('Location: ./index.php');
        exit;
    }
    // 認証が失敗したとき
    $result = false;
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Login</title>
</head>
<body>
<h3>Please Login</h3>
<form method="post" action="<?php echo basename(__FILE__); ?>">
    Address: <input type="text" name="user_id" value="" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="Login ID" required>
    Password: <input type="password" name="pw" value="" minlength="<?php echo LENGTH_PW_MIN;?>" maxlength="<?php echo LENGTH_PW_MAX;?>" placeholder="Password" required>
    <input type="hidden" name="token" value="<?php echo entity(generate_token()); ?>">
    <input type="submit" value="Login">
</form>
<?php if ( $result == false ): ?>
<p style="color: red;">ユーザ名またはパスワードが違います</p>
<?php endif; ?>
[ <a href="reset.php">Reset</a> ]
[ <a href="entry.php">Entry</a> ]
[ <a href="twitter_login.php">Twitter Login</a> ]
</body>
</html>