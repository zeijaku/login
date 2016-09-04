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
$result = "";
$response = "";

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $result = search_user_id($user_id);

    if( $result ) {
        // 有効期間生成
        $lastmonth = new DateTime('+' . MAIL_RESET_LIMIT . ' hour');
        $limit = $lastmonth->format('Y-m-d H:m:s');

        // ptoken生成
        $ptoken = encryption($limit . ' ' . $user_id . ' ' . $result['id']);

        //UTF-8環境
        $mailfrom = mb_encode_mimeheader(MAIL_FROM) . '<' . MAIL_FROM_DOMAIN . '>';
        
        $header = "MIME-Version: 1.0\n"
        . "Content-Transfer-Encoding: 7bit\n"
        . "Content-Type: text/plain; charset=ISO-2022-JP\n"
        . "Message-Id: <" . md5(uniqid(microtime())) . "@" . MAIL_FROM_DOMAIN . ">\n"
        . "From: ".$mailfrom."\n";
        $message = "パスワード変更に関する設定アドレスをメールにて送信させていただきました\n\n";
        $message .= "下記アドレスにアクセスしてパスワード設定を変更してください\n";
        $message .= $_SERVER['SERVER_NAME'] . '/' . basename(__DIR__) . '/' . "reset_pw.php?ptoken=" . base64_encode($ptoken) . "\n\n";
        $message .= "当メールは" . MAIL_RESET_LIMIT . "時間の間だけ有効です\n";
        $message .= "こちらのメールに心当たりのない方は下記までご連絡ください";
        $message .= MAIL_FROM;
        $message = mb_convert_kana($message, "KVa");
        $message = mb_convert_encoding($message, "JIS", "auto");
     
        if (!mail($user_id, "パスワード再設定についてのお知らせ", $message, $header, "-f " . MAIL_FROM)) {
            //exit("error");
        }
        $response = true;
    } else {
        $response = false;
    }
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Password Reset</title>
</head>
<body>
<h3>Password Reset?</h3>
<form method="post" action="<?php echo basename(__FILE__); ?>">
    Address: <input type="text" name="user_id" value="" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="Login ID" required>
    <input type="hidden" name="token" value="<?php echo entity(generate_token()); ?>">
    <input type="hidden" name="id" value="<?php echo $_SESSION['id']; ?>">
    <input type="submit" value="Reset!!">
</form>
<?php if( $response === true ) {
    echo '<p style="color: red;">登録アドレスにメールを送信しました</p>';
} elseif( $response === false  ) {
    echo '<p style="color: red;">登録されているもしくは正しいアドレスを入力してください</p>';
}
?>
<a href="/login/login.php">Login</a>
</body>
</html>