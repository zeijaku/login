<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/common_functions.php';
require_once __DIR__ . '/common_functions_db.php';

// ログインチェック
require_logined_session();

header('Content-Type: text/html; charset=UTF-8');

// 設定変更
if ( $user_id = (filter_input(INPUT_POST, 'delete', FILTER_SANITIZE_SPECIAL_CHARS)) ) {
    user_delete_admin($user_id);
} elseif ( $user_id = (filter_input(INPUT_POST, 'resurrect', FILTER_SANITIZE_SPECIAL_CHARS)) ) {
    user_resurrect_admin($user_id);
}

// 全ユーザー検索
$search_all = search_all();

// 初期化
$line = "1";
$list = "";

// 表示整形
foreach( $search_all as $result ) { 
    // 背景色設定
    $color = " style='background-color: #ccc;' ";
    if( $result['delete_state'] == "1" ) {
        // 削除済
        $color = " style='background-color: #fc3;' ";
    } elseif( ($line % 2) == "0" ) {
        $color = " style='background-color: #eee;' ";
    }
    // ボタン設定
    $state = "delete";
    $state_view = "×";
    if( $result['delete_state'] == "1" ) {
        // 復活
        $state = "resurrect";
        $state_view = "○";
    }
    $safety = "";
    if( $_SESSION['id'] == $result['id'] ) {
        // 自身は削除不可
        $safety = "disabled";
    }
    $list .= "<tr $color><td><button type='submit' name='" . $state . "' value='" . $result['id'] . "' " . $safety . " onClick=\"return confirm('変更しても良いですか？')\">" . $state_view . "</button></td><td>" . $result['id'] . "</td><td>" . $result['create_day'] . "</td><td>" . $result['update_day'] . "</td><td>" . $result['delete_day'] . "</td><td>" . $result['delete_state'] . "</td><td><input type=\"text\" name=\"login_id\" value=\"" . $result['user_id'] . "\"></td><td><input type=\"text\" value=\"" . $result['user_name'] . "\"></td><td><input type=\"text\" name=\"pw\" value=\"" . $result['pw'] . "\"></td><td><input type=\"text\" value=\"" . $result['type'] . "\"></td><td>" . $result['auth'] . "</td></tr>\n";
    $line++;
}

?>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Admin-only page</title>
</head>
<body>
[ <a href="./index.php">Return</a> ]
<h3>Welcome - <?php echo $_SESSION['username']; ?></h3>

<form method="post" action="<?php echo basename(__FILE__); ?>">
<table>
<th></th><th>No</th><th>create</th><th>update</th><th>delete</th><th>flag</th><th>user_id</th><th>user_name</th><th>pw</th><th>type</th><th>auth</th>
<?php echo $list; ?>
</table>
</form>
</body>
</html>