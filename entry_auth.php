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
if ( $atoken = (filter_input(INPUT_GET, 'atoken', FILTER_SANITIZE_SPECIAL_CHARS)) ) {

    // atoken分解
    $atoken_dec = explode(' ', decryption(base64_decode($atoken)));

    $limit = $atoken_dec['0'] . ' ' . $atoken_dec['1'];
    $user_id = $atoken_dec['2'];
    $id = $atoken_dec['3'];

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
    } 

    // success認証用SESSION
    $_SESSION['id'] = $id;

} else {
    // GETメソッド無しの場合
    header('Location: ./login.php');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Entry  Confirm</title>
</head>
<body>
<h3>Entry Confirm - <?php echo $user_id; ?></h3>
<form method="post" action="<?php echo "entry_success.php" ?>">
    <input type="hidden" name="user_id" value="<?php echo encryption($user_id); ?>">
    <input type="hidden" name="atoken" value="<?php echo $atoken; ?>">
    <button type="submit">Are you Entry?</button>
</form>
<?php if ( $error === false ): ?>
<p style="color: red;"><?php echo $error; ?></p>
<p style="color: red;">完了できませんでした。</p>
<?php endif; ?>
<a href="./login.php">Login</a>
</body>
</html>