<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

session_start();
require_once 'twitteroauth/autoload.php';

// 「Twitterのコンシュマーキー」と「Twitterのコンシュマーシークレットキー」を使ってインスタンス化
$twitter = new Abraham\TwitterOAuth\TwitterOAuth(
            CONSUMERKEY, 
            CONSUMERSECRET
        );
 
//コールバックURLをセットして認証トークンのリクエストを発行
$request_token = $twitter->oauth('oauth/request_token', array('oauth_callback' => CALLBACK));
 
// 上記で受け取った「oauth_token」と「oauth_token_secret」をセッションに代入
// ここでセッションに入れる理由はcallback.phpで認証を行うためです。
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
// Twitterの認証画面へリダイレクト
$url = $twitter->url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));
 
header('location: '. $url);
exit;