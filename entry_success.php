<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_unlogined_session();
 
// POSTメソッドのときのみ実行
if ( $user_id_enc = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS) && $_SESSION['id'] ) {

    // POST受信
    $atoken = decryption(base64_decode($_POST['atoken']));
    $user_id = decryption(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS));

    if( $_SESSION['id'] && search_user_id($user_id) ) {

        // ユーザー情報変更
        $result = user_auth_change($_SESSION['id']);

        // セッション用Cookieの破棄
        setcookie(session_name(), '', 1);
        // セッションファイルの破棄
        session_destroy();
    } else {
        // パスワード不一致・指定文字数以下・全角混入
        header('Location: ./entry_auth.php?atoken=' . filter_input(INPUT_POST, 'atoken', FILTER_SANITIZE_SPECIAL_CHARS) . "&check=false");
        exit;
    }

} else {
    // POSTメソッド無しの場合
    header('Location: ./entry_auth.php?atoken=' . filter_input(INPUT_POST, 'atoken', FILTER_SANITIZE_SPECIAL_CHARS) . "&check=false1");
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Entry Success</title>
</head>
<body>
<h3>Entry Success!!</h3>

<?php if ( $error ): ?>
<p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
<a href="./login.php">Login</a>
</body>
</html>