<?php
/*
 * ***************************
 * PHP設定
 * ***************************
 */
/**
 * エラー（スクリプトの実行が中断される）のみ出力する場合
 */
ini_set( 'error_reporting', 0 );	// エラー非表示
//ini_set( 'error_reporting', -1 );	// 全エラー出力

/*
 * ***************************
 * 構成設定
 * ***************************
 */
/**
 * DataBase
 */
// 使用DB種類(sqlite / mysql)
define('DB_TYPE', "sqlite");
// SQLite
define('DB_USER', "userDB.sqlite");
// MySQL
define('DB_MYDQL_HOST', "127.0.0.1");
define('DB_MYSQL_DB', "userDB");
define('DB_MYSQL_USER', "root");
define('DB_MYSQL_PW', "");

// Twitter token
define('CONSUMERKEY', "");
define('CONSUMERSECRET', "");
// Twitter Callback URL
define('CALLBACK', '');

/**
 * メール設定
 */
// 再発行有効時間
define('MAIL_RESET_LIMIT', "8");
// メールヘッダー
define('MAIL_FROM', "info@zeijaku.net");
define('MAIL_FROM_DOMAIN', "zeijaku.net");
define('MAIL_FROM_NAME', "zeijaku");
/**
 * 入力文字数制限
 */
define('LENGTH_ID_MAX', "256");
define('LENGTH_ID_MIN', "6");
define('LENGTH_PW_MAX', "256");
define('LENGTH_PW_MIN', "6");