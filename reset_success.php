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

// POSTメソッドのときのみ実行
if ( $user_id_enc = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS) && $_SESSION['id'] ) {

    // POST受信
    $user_id = decryption($user_id_enc);
    $pw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS);
    $repw = filter_input(INPUT_POST, 'repw', FILTER_SANITIZE_SPECIAL_CHARS);

    // バリデーションチェック
    $vpw = validate_pw($pw);
    $vrepw = validate_pw($repw);

    if( $pw == $repw && $vpw === true && $vrepw === true && search_user_id($user_id) ) {

        // ユーザー情報変更
        $result = user_pw_change($pw, $_SESSION['id']);

        // セッション用Cookieの破棄
        setcookie(session_name(), '', 1);
        // セッションファイルの破棄
        session_destroy();
    } else {
        // パスワード不一致・指定文字数以下・全角混入
        header('Location: ./reset_pw.php?ptoken=' . filter_input(INPUT_POST, 'ptoken', FILTER_SANITIZE_SPECIAL_CHARS) . "&check=false");
        exit;
    }

} else {
    // POSTメソッド無しの場合
    header('Location: ./reset_pw.php?ptoken=' . filter_input(INPUT_POST, 'ptoken', FILTER_SANITIZE_SPECIAL_CHARS) . "&check=false");
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Password Reset</title>
</head>
<body>
<h3>Password Reset!!</h3>

<?php if ( $error ): ?>
<p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
<a href="./login.php">Login</a>
</body>
</html>