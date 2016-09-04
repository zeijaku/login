<?php
/**
 * ログイン状態によってリダイレクトを行うsession_startのラッパー関数
 * 初回時または失敗時にはヘッダを送信してexitする
 */
function require_unlogined_session() {
    // セッション開始
    @session_start();
    // ログインしていれば / に遷移
    if (isset($_SESSION['username'])) {
        header('Location: ./');
        exit;
    }
}
function require_logined_session() {
    // セッション開始
    @session_start();
    // ログインしていなければ /login.php に遷移
    if (!isset($_SESSION['username'])) {
        header('Location: ./login.php');
        exit;
    }
}
/**
 * CSRFトークンの生成
 *
 * @return string トークン
 */
function generate_token() {
    // セッションIDからハッシュを生成
    return hash('sha256', session_id());
}
/**
 * エンティティーラッパー関数
 *
 * @param string $str
 * @return string
 */
function entity($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
/**
 * CSRFトークンの検証
 *
 * @param string $token
 * @return bool 検証結果
 */
function validate_token($token) {
    // 送信されてきた$tokenがこちらで生成したハッシュと一致するか検証
    return $token === generate_token();
}
/**
 * POST検証(user_id)
 *
 * @param string $user_id
 * @return bool 検証結果
 */
function validate_user_id($user_id) {
    $result = true;
    if (!$user_id) {
        $result = '未記入もしくは有効なIDではありません';
    }
    if (strlen($user_id) > LENGTH_ID_MAX) {
        $result = '' . LENGTH_ID_MAX . '文字以内にしてください';
    }
    return $result;
}
/**
 * POST検証(user_name)
 *
 * @param string $user_name
 * @return bool 検証結果
 */
function validate_user_name($user_name) {
    $result = true;
    if (!$user_name) {
        $result = '未記入もしくは有効なIDではありません';
    }
    if (strlen($user_name) > LENGTH_ID_MAX) {
        $result = '' . LENGTH_ID_MAX . '文字以内にしてください';
    }
    return $result;
}
/**
 * POST検証(PW)
 *
 * @param string $pw
 * @return bool 検証結果
 */
function validate_pw($pw) {
    $result = true;
    if (!$pw) {
        $result = 'パスワードを入力してください';
    }
    if (!empty($pw) && strlen($pw) <= LENGTH_PW_MIN) {
        $result = 'パスワードが短すぎます';
    }
    if (!(strlen($pw) === mb_strlen($pw))) {
        $result = 'パスワードに全角が含まれています。すべて半角で入力してください';
    }
    return $result;
}
/**
 * 暗号化
 *
 * @param string $value
 */
function encryption($value) {
    $password   = sha1(uniqid(mt_rand(),true)); 
    $iv         = base64_encode(openssl_random_pseudo_bytes(12));
    $raw_output = false; 
    $method     = 'AES-256-CBC'; 
    $encode_tmp = openssl_encrypt($value, $method, $password, $raw_output, $iv);
    $encode     = $encode_tmp . $password . $iv;
    return $encode;
}
function decryption($value) {
    $decode     = substr($value, 0, -56);
    $password   = substr($value, -56, -16);
    $iv         = substr($value, -16);
    $raw_output = false; 
    $method     = 'AES-256-CBC'; 
    $decode     = openssl_decrypt($decode, $method, $password, $raw_output, $iv);
    return $decode;
}
/**
 * ユニーク英数字作成
 * @param string $length
 */
function create_uniq_wordnum($length) {
    // ランダム英数生成
    $uniq_id = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890'), 0, $length);
    return $uniq_id;
}
/**
 * Twitter Login
 */
function login_twitter() {

        
}