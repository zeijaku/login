<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_unlogined_session();

// 初期化
$result = "";
$error = "";
    
// GETメソッドのときのみ実行
if ( $ptoken = (filter_input(INPUT_GET, 'ptoken', FILTER_SANITIZE_SPECIAL_CHARS)) ) {

    // ptoken分解
    $ptoken_dec = explode(' ', decryption(base64_decode($ptoken)));

    $limit = $ptoken_dec['0'] . ' ' . $ptoken_dec['1'];
    $user_id = $ptoken_dec['2'];
    $id = $ptoken_dec['3'];

    $result = search_user_id($user_id);

    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');

    if( !$result ) {
        // GETはあるが該当するものが無い場合
        header('Location: ./login.php');
        exit;
    } elseif( $limit < $date ) {
        // GETはあるが有効期間が過ぎている場合
        header('Location: ./login.php');
        exit;
    } else {
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        $_SESSION['id'] = $id;
    }

} else {
    // GETメソッド無しの場合
    header('Location: ./login.php');
    exit;
}
// 変更時エラー戻り処理
if ( $error = (filter_input(INPUT_GET, 'check', FILTER_SANITIZE_SPECIAL_CHARS)) ) {
    $error = false;
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Password Reset</title>
</head>
<body>
<h3>Password Reset? - <?php echo $user_id; ?></h3>
<form method="post" action="<?php echo "reset_success.php" ?>">
    Password: <input type="text" name="pw" value="">
    Password Re: <input type="text" name="repw" value="">
    <input type="hidden" name="user_id" value="<?php echo encryption($user_id); ?>">
    <input type="hidden" name="ptoken" value="<?php echo $ptoken; ?>">
    <input type="submit" value="Reset!!">
</form>
<?php if ( $error === false ): ?>
<p style="color: red;"><?php echo $error; ?></p>
<p style="color: red;">変更できませんでした。入力内容を確認してください</p>
<?php endif; ?>
<a href="./login.php">Login</a>
</body>
</html>