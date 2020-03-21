<?php

/**
 * 添加分类的操作.
 *
 * @return string
 */
function addCate()
{
    $arr = $_POST;
    if ($arr['pid'] > 0) {
        $sql = 'select path,level from cate where id=?';
        $type = 'i';
        $data = array($arr['pid']);
        $row = fetchOne($sql, $type, $data);
    } else {
        $row = array('path' => '0', 'level' => 0);
    }
    $id = insert('cate', $arr, 'is');
    if ($id) {
        $temp = array('path' => $row['path'].','.$id, 'level' => $row['level'] + 1);
        $res = update('cate', $temp, "id={$id}");
        $mes = "分类添加成功!<br/><a href='addCate.php'>继续添加</a>|<a href='listCate.php'>查看分类</a>";
    } else {
        $mes = "分类添加失败！<br/><a href='addCate.php'>重新添加</a>|<a href='listCate.php'>查看分类</a>";
    }

    return $mes;
}

/**
 * 根据ID得到指定分类信息.
 *
 * @param int $id
 *
 * @return array
 */
function getCateById($id)
{
    $sql = 'select id,cName from cate where id=?';
    $type = 'i';
    $data = array($id);

    return fetchOne($sql, $type, $data);
}

/**
 * 修改分类的操作.
 *
 * @param string $where
 *
 * @return string
 */
function editCate($where)
{
    $arr = $_POST;
    if (update('cate', $arr, $where)) {
        $mes = "分类修改成功!<br/><a href='listCate.php'>查看分类</a>";
    } else {
        $mes = "分类修改失败!<br/><a href='listCate.php'>重新修改</a>";
    }

    return $mes;
}

/**
 *删除分类.
 *
 * @param string $where
 *
 * @return string
 */
function delCate($id)
{
    $res = checkProExist($id);
    if (!$res) {
        $where = 'id='.$id;
        $arr = getCateById($id);
        $parent = 'pid='.$arr['id'];
        if (delete('cate', $parent) && delete('cate', $where)) {
            $mes = "分类删除成功!<br/><a href='listCate.php'>查看分类</a>|<a href='addCate.php'>添加分类</a>";
        } else {
            $mes = "删除失败！<br/><a href='listCate.php'>请重新操作</a>";
        }

        return $mes;
    } else {
        alertMes('不能删除分类，请先删除该分类下的商品', 'listPro.php');
    }
}

/**
 * 得到所有分类.
 *
 * @return array
 */
function getAllCate()
{
    $sql = 'select id,cName,pid,path,level from cate order by path asc';
    @$rows = fetchAll($sql, '', array());

    return $rows;
}