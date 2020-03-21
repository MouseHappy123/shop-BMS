<?php

/**
 * 连接数据库.
 *
 * @return resource
 */
function connect()
{
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PWD) or die('数据库连接失败Error:'.mysql_errno().':'.mysql_error());
    mysqli_set_charset($link, DB_CHARSET);
    mysqli_select_db($link, DB_DBNAME) or die('指定数据库打开失败');

    return $link;
}

/**
 * 完成记录插入的操作.
 *
 * @param string $table
 * @param array  $array
 * @param string $type
 *
 * @return number
 */
function insert($table, $array, $type)
{
    $keys = join(',', array_keys($array));
    $vals = "'".join("','", array_values($array))."'";
    $placeholder = array();
    foreach ($array as $k => $v) {
        array_push($placeholder, '?');
    }
    $holders = join(',', array_values($placeholder));
    $sql = "insert {$table}($keys) values({$holders})";
    $link = connect();

    $stmt = mysqli_prepare($link, $sql);
    $data = array_values($array);
    array_unshift($data, $stmt, $type);
    call_user_func_array('mysqli_stmt_bind_param', refValues($data));
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return mysqli_insert_id($link);
}

/**
 * 记录的更新操作.
 *
 * @param string $table
 * @param array  $array
 * @param string $where
 *
 * @return number
 */
function update($table, $array, $where = null)
{
    $str = '';
    foreach ($array as $key => $val) {
        if ($str == null) {
            $sep = '';
        } else {
            $sep = ',';
        }
        $str .= $sep.$key."='".$val."'";
    }
    $sql = "update {$table} set {$str} ".($where == null ? null : ' where '.$where);
    $link = connect();
    $result = mysqli_query($link, $sql);
    //var_dump($result);
    //var_dump(mysql_affected_rows());exit;
    if ($result) {
        return mysqli_affected_rows($link);
    } else {
        return false;
    }
}

/**
 *	删除记录.
 *
 * @param string $table
 * @param string $where
 *
 * @return number
 */
function delete($table, $where = null)
{
    $where = $where == null ? null : ' where '.$where;
    $sql = "delete from {$table} {$where}";
    $link = connect();
    mysqli_query($link, $sql);

    return mysqli_affected_rows($link);
}

/**
 *得到指定一条记录.
 *
 * @param string $sql
 * @param string $type
 * @param array  $data
 * @param string $result_type
 *
 * @return multitype:
 */
function fetchOne($sql, $type, $data, $result_type = MYSQLI_ASSOC)
{
    $link = connect();
    $stmt = mysqli_prepare($link, $sql);
    array_unshift($data, $stmt, $type);
    call_user_func_array('mysqli_stmt_bind_param', refValues($data));
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    $row = mysqli_fetch_array($result, $result_type);

    return $row;
}

/**
 * 得到结果集中所有记录 ...
 *
 * @param string $sql
 * @param string $type
 * @param array  $data
 * @param string $result_type
 *
 * @return multitype:
 */
function fetchAll($sql, $type, $data, $result_type = MYSQLI_ASSOC)
{
    $link = connect();
    $stmt = mysqli_prepare($link, $sql);
    array_unshift($data, $stmt, $type);
    call_user_func_array('mysqli_stmt_bind_param', refValues($data));
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    $row = array();
    $rows = array();
    while (@$row = mysqli_fetch_array($result, $result_type)) {
        $rows[] = $row;
    }

    return $rows;
}

/**
 * 得到结果集中的记录条数.
 *
 * @param unknown_type $sql
 *
 * @return number
 */
function getResultNum($sql)
{
    $link = connect();
    $result = mysqli_query($link, $sql);

    return mysqli_num_rows($result);
}

/**
 * 得到上一步插入记录的ID号.
 *
 * @return number
 */
function getInsertId()
{
    $link = connect();

    return mysqli_insert_id($link);
}

function refValues($arr)
{
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
        $refs = array();
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }

        return $refs;
    }

    return $arr;
}
