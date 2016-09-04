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
$error = "";
$response = "";

// POSTメソッドのときのみ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // POST処理
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $pw = filter_input(INPUT_POST, 'pw', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Validationチェック
    $error_user_id = validate_user_id($user_id);
    $error_user_name = validate_user_name($user_name);
    $error_pw = validate_pw($pw);
    // 重複登録チェック
    $error_id = search_user_id($user_id);
    $error_name = search_user_name($user_name);

    if( $error_user_id === true && $error_user_name === true && $error_pw === true && $error_id === false && $error_name === false ) {
        $type = "normal";
        // ユーザー登録
        $result = entry($user_id, $user_name, $pw, $type);

        if( isset($result) ) {
            // 登録が成功したとき

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
            $message = "登録の確認についてメールにて送信させていただきました\n\n";
            $message .= "下記アドレスにアクセスして登録を完了してください\n";
            $message .= $_SERVER['SERVER_NAME'] . '/' . basename(__DIR__) . '/' . "entry_auth.php?atoken=" . base64_encode($ptoken) . "\n\n";
            $message .= "当メールは" . MAIL_RESET_LIMIT . "時間の間だけ有効です\n";
            $message .= "こちらのメールに心当たりのない方は下記までご連絡ください";
            $message .= MAIL_FROM;
            $message = mb_convert_kana($message, "KVa");
            $message = mb_convert_encoding($message, "JIS", "auto");
     
            if (!mail($user_id, "登録確認についてのお知らせ", $message, $header, "-f " . MAIL_FROM)) {
                //exit("error");
            }
            $response = "確認メールを送信しました";

            // セッションIDの追跡を防ぐ
//            session_regenerate_id(true);
            // ユーザ名をセット
//            $_SESSION['username'] = $user_name;
//            $_SESSION['id'] = $result['id'];

            // ログイン完了後に / に遷移
//            header('Location: ./index.php');
//            exit;
        } else {
            $response = "登録出来ませんでした";
        }

    } else {
        $error = true;
    }
}

header('Content-Type: text/html; charset=UTF-8');

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Entry</title>
</head>
<body>
<h4>Entry</h4>
<form method="post" action="<?php echo basename(__FILE__); ?>">
    Login ID: <input type="text" name="user_id" value="" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="Login ID" required><br />
    UserName: <input type="text" name="user_name" value="" minlength="<?php echo LENGTH_ID_MIN;?>" maxlentgh="<?php echo LENGTH_ID_MAX;?>" placeholder="User Name" required><br />
    Password: <input type="password" name="pw" value="" minlength="<?php echo LENGTH_PW_MIN;?>" maxlength="<?php echo LENGTH_PW_MAX;?>" placeholder="Password" required><br />
    <button type="submit">Entry</button>
    <?php echo $response; ?>
</form>
<?php if ( $error === true ): ?>
<p style="color: red;">入力内容を確認してください</p>
<?php endif; ?>
<a href="./login.php">Login</a>
</body>
</html>