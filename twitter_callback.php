<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

session_start();
// TwitterOAuthのコアファイルとTwitterアプリケーションの設定値を読み込み
require_once 'twitteroauth/autoload.php';
 
// セッション情報を変数に代入
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
 
// 本人確認
if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
    die( 'Identification Error' );
    exit;
}
 
//OAuth トークンも用いて TwitterOAuth をインスタンス化
$twitter = new Abraham\TwitterOAuth\TwitterOAuth(
                    CONSUMERKEY, 
                    CONSUMERSECRET, 
                    $request_token['oauth_token'], 
                    $request_token['oauth_token_secret']
            );
 
// tokenを取得
$result = $twitter->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
 
$getUser = new Abraham\TwitterOAuth\TwitterOAuth(
                    CONSUMERKEY, 
                    CONSUMERSECRET, 
                    $result['oauth_token'], 
                    $result['oauth_token_secret']
            );
 
// ユーザ情報取得
$user = $getUser->get("account/verify_credentials");

if( isset($user->screen_name) ) {

    // ユーザー登録確認
    if(  search_user_id($user->id) ) {
        // 認証が成功したとき
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        // ユーザ名をセット
        $_SESSION['username'] = $user->screen_name;
        $_SESSION['id'] = $result['id'];
    } else {
        // ユーザー登録
        $result = entry($user->id, $user->screen_name, $_SESSION['oauth_token'], 'twitter');
        user_auth_change($result['id']);
        
        // 認証が成功したとき
        // セッションIDの追跡を防ぐ
        session_regenerate_id(true);
        // ユーザ名をセット
        $_SESSION['username'] = $user->screen_name;
        $_SESSION['id'] = $result['id'];
    }
    // ログイン完了後に / に遷移
    header('Location: ./index.php');
    exit;
}

header('Location: ./login.php');
exit;