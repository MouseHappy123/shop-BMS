<?php

function reg()
{
    require_once 'swiftmailer-master/lib/swift_required.php';
    $emailPassword = ''; //密码
    $arr = $_POST;
    $arr['password'] = md5($_POST['password']);
    $arr['regTime'] = time();
    $arr['token'] = md5($arr['username'].$arr['password'].$arr['regTime']);
    $arr['token_exptime'] = $arr['regTime'] + 24 * 3600; //过期时间
    $uploadFile = uploadFile();

    //print_r($uploadFile);
    if ($uploadFile && is_array($uploadFile)) {
        $arr['face'] = $uploadFile[0]['name'];
    } else {
        return '注册失败';
    }
    //	print_r($arr);exit;
    if (insert('user', $arr, 'ssssssss')) {
        //发送邮件，以QQ邮箱为例
        //配置邮件服务器，得到传输对象
        $transport = Swift_SmtpTransport::newInstance('smtp.qq.com', 25);
        //设置登陆帐号和密码
        $transport->setUsername('');
        $transport->setPassword($emailPassword);
        //得到发送邮件对象Swift_Mailer对象
        $mailer = Swift_Mailer::newInstance($transport);
        //得到邮件信息对象
        $message = Swift_Message::newInstance();
        //设置管理员的信息
        $message->setFrom(array('' => 'admin'));
        //将邮件发给谁
        $message->setTo(array($arr['email'] => 'user'));
        //设置邮件主题
        $message->setSubject('激活邮件');
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/shop/doAction.php'."?act=active&token={$arr['token']}";
        $urlencode = urlencode($url);
        $str = <<<EOF
		亲爱的{$arr['username']}您好~！感谢您注册我们网站<br/>
		请点击此链接激活帐号即可登陆！<br/>
		<a href="{$url}">{$urlencode}</a>
		<br/>
		如果点此链接无反映，可以将其复制到浏览器中来执行，链接的有效时间为24小时。		
EOF;
        $message->setBody("{$str}", 'text/html', 'utf-8');
        try {
            if ($mailer->send($message)) {
                $mes = "恭喜您{$arr['username']}注册成功，请到邮箱激活之后登陆<br/>3秒钟后跳转到登陆页面";
                echo '<meta http-equiv="refresh" content="3;url=login.php"/>';
            } else {
                $PdoMySQL->delete($table, 'id='.$lastInsertId);
                echo '注册失败，请重新注册';
                echo '3秒钟后跳转到注册页面';
                echo '<meta http-equiv="refresh" content="3;url=login.php"/>';
            }
        } catch (Swift_ConnectionException $e) {
            echo '邮件发送错误'.$e->getMessage();
        }
    } else {
        $filename = 'uploads/'.$uploadFile[0]['name'];
        if (file_exists($filename)) {
            unlink($filename);
        }
        $mes = "注册失败!<br/><a href='reg.php'>重新注册</a>|<a href='index.php'>查看首页</a>";
    }

    return $mes;
}
function login()
{
    $username = $_POST['username'];
    //addslashes():使用反斜线引用特殊字符
    $username = addslashes($username);
    // $username = mysql_escape_string($username);
    $password = md5($_POST['password']);
    $sql = 'select * from user where username=? and password=?';
    $type = 'ss';
    $data = array($username, $password);
    //$resNum=getResultNum($sql);
    $row = fetchOne($sql, $type, $data);
    //echo $resNum;
    if ($row) {
        if ($row['status'] == 1) {
            $_SESSION['loginFlag'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $mes = "登陆成功！<br/>3秒钟后跳转到首页<meta http-equiv='refresh' content='3;url=index.php'/>";
        } else {
            $mes = "登陆失败！请先激活在登陆!<br/><a href='login.php'>重新登陆</a>";
        }
    } else {
        $mes = "用户不存在!<br/><a href='login.php'>重新登陆</a>";
    }

    return $mes;
}

function userOut()
{
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 1);
    }

    session_destroy();
    header('location:index.php');
}

function active()
{
    $token = addslashes($_GET['token']);
    $row = fetchOne('select * from user where token=? AND status=0', 's', array($token));
    $now = time();
    if ($now > $row['token_exptime']) {
        $mes = '激活时间过期，请重新登陆激活';
    } else {
        $res = update('user', array('status' => 1), 'id='.$row['id']);
        if ($res) {
            $mes = '激活成功,3秒钟后跳转到登陆页面<br/><meta http-equiv="refresh" content="3;url=login.php"/>';
        } else {
            $mes = '激活失败，请重新激活<br/><meta http-equiv="refresh" content="3;url=index.php"/>';
        }
    }

    return $mes;
}
function getUserByPage($page, $pageSize = 5)
{
    $sql = 'select * from user';
    global $totalUserRows;
    $totalUserRows = getResultNum($sql);
    global $totalUserPage;
    $totalUserPage = ceil($totalUserRows / $pageSize);
    if ($page < 1 || $page == null || !is_numeric($page)) {
        $page = 1;
    }
    if ($page >= $totalUserPage) {
        $page = $totalUserPage;
    }
    $offset = ($page - 1) * $pageSize;
    $sql = 'select id,username,email,status from user limit ?,?';
    $type = 'ii';
    $data = array($offset, $pageSize);
    $rows = fetchAll($sql, $type, $data);

    return $rows;
}
