<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>生成单元测试</title>
    <meta charset="utf-8">
</head>
<body>
<h3>请输入controllers下的带目录不带后缀的文件名称，例：ljorder/checkFinalSubmit</h3>
<form action="/index.php/Home/Phpunit/createUnit">
    接口地址:<input type="text" name="fileName" value="dining/"><br><br>
    请求方法:<input type="text" name="function" value="index"><br><br>
    请求方式:<input type="text" name="method" value="get"><br><br>
    <input type="submit" value="提交">
</form>
</body>
</html>