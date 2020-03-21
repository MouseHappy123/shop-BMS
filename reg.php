<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>注册</title>
<link type="text/css" rel="stylesheet" href="styles/reset.css">
<link type="text/css" rel="stylesheet" href="styles/main.css">
<!--[if IE 6]>
<script type="text/javascript" src="js/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript" src="js/ie6Fixpng.js"></script>
<![endif]-->
</head>

<body>
<div class="headerBar">
	<div class="logoBar red_logo">
		<div class="comWidth">
			<div class="logo fl">
				<a href="#"><img src="images/logo.jpg" style="width: 60px;"></a>
			</div>
			<h3 class="welcome_title">欢迎注册</h3>
		</div>
	</div>
</div>

<div class="regBox">
	<div class="login_cont">
	<form method="post" enctype="multipart/form-data" action="doAction.php?act=reg" >
		<ul class="login">
			<li><span class="reg_item"><i>*</i>用户名：</span><div class="input_item"><input type="text"  name="username"  placeholder="请输入用户名" class="login_input user_icon" required="required"></div></li>
			<li><span class="reg_item"><i>*</i>密码：</span><div class="input_item"><input type="password"  name="password"   class="login_input user_icon"required="required"></div></li>
			<li><span class="reg_item"><i>*</i>邮箱：</span><div class="input_item"><input type="email" name="email" placeholder="请输入合法邮箱" class="login_input user_icon"required="required"></div></li>
			<li><span class="reg_item"><i>*</i>性别：</span><div class="input_item">
			<input type="radio"  name="sex" value="1"> 男
			<input type="radio"  checked="checked" name="sex" value="2" > 女
			<input type="radio"  name="sex" value="3" > 保密
			</div></li>
			<li><span class="reg_item"><i>*</i>头像：</span><div class="input_item"><input type="file"  name="myFace" ></div></li>
			<li class="autoLogin"><span class="reg_item">&nbsp;</span><input type="checkbox" id="t1" class="checked"><label for="t1">我同意XX条款</label></li>
			<li><span class="reg_item">&nbsp;</span><button style="border:none;width:266px;height:35px;outline:none;"><img src="images/reg.jpg" alt="" /></button></li>
		</ul>
		</form>
	</div>
</div>

<div class="hr_25"></div>
<div class="footer">
</div>
</body>
</html>
