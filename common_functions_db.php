<?php

// --------------------------------------------------
// DB接続 - ユーザー情報
// --------------------------------------------------
function conn_userDB( $TYPE = DB_TYPE)
{
    // 接続DB選択
    if( $TYPE == 'sqlite' )
    {
        $conn = new PDO("sqlite:" . DB_USER);
    }
    elseif( $TYPE == 'mysql' )
    {
        // MySQL
        try
        {
            $conn = new PDO("mysql:host=" . DB_MYDQL_HOST . "; dbname=" . DB_MYSQL_DB, DB_MYSQL_USER, DB_MYSQL_PW);
        }
        catch(PDOException $e)
        {
            var_dump($e->getMessage());
        }
    }
    else
    {
        $conn = new PDO("sqlite:" . DB_USER);
    }
    return $conn;
}
// --------------------------------------------------
// データベースの初期設定 - ユーザー情報
// --------------------------------------------------
function create_userDB()
{
    // データベースに接続
    $conn = new PDO("sqlite:" . DB_USER);

    $sql = "CREATE TABLE IF NOT EXISTS userDB (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                create_day TEXT NOT NULL,
                update_day TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
                delete_day TEXT NOT NULL DEFAULT '0000-00-00 00:00:00',
                delete_state TEXT NOT NULL DEFAULT 0,
                user_id TEXT NOT NULL,
                user_name TEXT NOT NULL,
                pw TEXT NOT NULL,
                type TEXT NOT NULL,
                auth TEXT NOT NULL DEFAULT 0
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->closeCursor();

    return $conn;
}
// --------------------------------------------------
// DB追加
// --------------------------------------------------
/*
 * ********************
 * ユーザー情報 追加
 * ********************
 */
function entry($user_id, $user_name, $pw, $type = "normal")
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    // ユーザー情報
    $sql_insert = "INSERT INTO userDB(
        create_day,
        user_id,
        user_name,
        pw,
        type
    )
    VALUES
    (
        '$date',
        '" . $user_id . "',
        '" . $user_name . "',
        '" . $pw . "',
        '" . $type . "'
    )";
    $stmt_user = $conn->prepare($sql_insert);
    $stmt_user->execute();
    $id['id'] = $conn->lastInsertId();
    $stmt_user = null;

    return $id;
}
// --------------------------------------------------
// DB検索
// --------------------------------------------------
/*
 * ********************
 * ログインチェック
 * ********************
 */
function login($user_id, $pw)
{
    $id_search = "";
    // メールアドレスとパスワードチェック
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $search_id = "SELECT * from userDB WHERE delete_day='0000-00-00 00:00:00' and user_id=? and pw=? and auth='1' LIMIT 1";
    $stmt_search = $conn->prepare($search_id);
    $stmt_search->execute(array($user_id, $pw));
    $id_search = $stmt_search->fetch();
    $stmt_search = null;

    return $id_search;
}
/*
 * ********************
 * 全検索
 * ********************
 */
function search_all()
{
    $result = array();
    // メールアドレスとパスワードチェック
    $conn = conn_userDB();
    $search_id = "SELECT * from userDB";
    $stmt_search = $conn->prepare($search_id);
    $stmt_search->execute();
    //$resut = $stmt_search->fetch();

    while( $result_row = $stmt_search->fetch() )
    {
        // 登録有り
        $result[$result_row['id']] = $result_row;
    }
    $stmt_search = null;

    return $result;
}
/*
 * ********************
 * 検索 user_id(生存idのみ)
 * ********************
 */
function search_id($id)
{
    // メールアドレスチェック
    $conn = conn_userDB();
    $search_user_id = "SELECT * from userDB WHERE delete_day='0000-00-00 00:00:00' and delete_state='0' and id=? LIMIT 1";
    $stmt_search = $conn->prepare($search_id);
    $stmt_search->execute(array($id));
    $result = $stmt_search->fetch();
    $stmt_search = null;

    return $result;
}
/*
 * ********************
 * 検索 user_id(生存user_idのみ)
 * ********************
 */
function search_user_id($user_id)
{
    // メールアドレスチェック
    $conn = conn_userDB();
    $search_user_id = "SELECT * from userDB WHERE delete_day='0000-00-00 00:00:00' and delete_state='0' and user_id=? LIMIT 1";
    $stmt_search = $conn->prepare($search_user_id);
    $stmt_search->execute(array($user_id));
    $result = $stmt_search->fetch();
    $stmt_search = null;

    return $result;
}
/*
 * ********************
 * 検索 user_name(生存user_nameのみ)
 * ********************
 */
function search_user_name($user_name)
{
    // メールアドレスチェック
    $conn = conn_userDB();
    $search_user_id = "SELECT * from userDB WHERE delete_day='0000-00-00 00:00:00' and delete_state='0' and user_name=? LIMIT 1";
    $stmt_search = $conn->prepare($search_user_id);
    $stmt_search->execute(array($user_name));
    $result = $stmt_search->fetch();
    $stmt_search = null;

    return $result;
}
// --------------------------------------------------
// DB変更
// --------------------------------------------------
/*
 * ********************
 * ユーザー情報 変更
 * ********************
 */
function user_conf_change($user_id, $user_name, $pw, $id)
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET update_day='" . $date . "', user_id=?, user_name=?, pw=? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $update = $stmt->execute(array($user_id, $user_name, $pw, $id));

    if( $update ) {
        $result = true;
    } else {
        $result = false;
    }
    $stmt = null;

    return $result;
}
/*
 * ********************
 * ユーザー情報 変更[PW]
 * ********************
 */
function user_pw_change($pw, $user_id)
{

    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET update_day='" . $date . "', pw=? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute(array($pw, $user_id));
    $result = $stmt->rowCount();
    $stmt = null;

    return $result;
}
/*
 * ********************
 * ユーザー情報 変更[PW]
 * ********************
 */
function user_auth_change($id)
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET auth='1' WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute(array($id));
    $result = $stmt->rowCount();
    $stmt = null;

    return $result;
}
/*
 * ********************
 * ユーザー情報 削除
 * ********************
 */
function user_delete($pw, $user_id)
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET delete_day='" . $date . "' , delete_state='1' WHERE pw=? and id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute(array($pw, $user_id));
    $result = $stmt->rowCount();
    $stmt = null;

    return $result;
}
/*
 * ********************
 * ユーザー情報 削除(Admin)
 * ********************
 */
function user_delete_admin($user_id)
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET delete_day='" . $date . "' , delete_state='1' WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute(array($user_id));
    $result = $stmt->rowCount();
    $stmt = null;

    return $result;
}
/*
 * ********************
 * ユーザー情報 復活(Admin)
 * ********************
 */
function user_resurrect_admin($user_id)
{
    $conn = conn_userDB();
    $now = new DateTime();
    $date = $now->format('Y-m-d H:m:s');
    $sql_update = "UPDATE userDB SET delete_day='0000-00-00 00:00:00' , delete_state='0' WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->execute(array($user_id));
    $result = $stmt->rowCount();
    $stmt = null;

    return $result;
}
