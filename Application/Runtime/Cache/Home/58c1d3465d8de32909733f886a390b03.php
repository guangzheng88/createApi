<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>测试接口返回值</title>
</head>
<style type="text/css">
    input{width: 400px;}
</style>
<body>
<?php if(!empty($file)): ?><p>
        单元测试文件生成成功，请将此内容粘贴至phpunit xml配置文件中<br>
        <textarea style="width: 800px;background:yellow;"><?php echo ($file); ?></textarea>
    </p><?php endif; ?>
<h1 style="text-align: center;">测试接口返回值</h1>
<form action="/index.php/Home/Api/submit" method="post" target="apiIframe" autocomplete="off">
    <table>
        <tr>
            <td>接口地址:</td>
            <td><input type="text"  name="bll_url" value="<?php echo ($apiUrl); ?>"></td>
        </tr>
        <tr>
            <td>请求方式</td>
            <td><input type="text" name="method" value="<?php echo ($method); ?>"></td>
        </tr>
        <tr>
            <td>参数名称</td>
            <td>参数值</td>
        </tr>
        <tr>
            <td><input type="text" name="key[]" style="width: 100px;" placeholder="请输入参数名称"></td>
            <td><input type="text" name="value[]" placeholder="请输入参数值"></td>
        </tr>

        <tr>
            <td><input type="text" name="key[]" style="width: 100px;" placeholder="请输入参数名称"></td>
            <td><input type="text" name="value[]" placeholder="请输入参数值"></td>
        </tr>
        <tr>
            <td><input type="text" name="key[]" style="width: 100px;" placeholder="请输入参数名称"></td>
            <td><input type="text" name="value[]" placeholder="请输入参数值"></td>
        </tr>
        <tr>
            <td><input type="text" name="key[]" style="width: 100px;" placeholder="请输入参数名称"></td>
            <td><input type="text" name="value[]" placeholder="请输入参数值"></td>
        </tr>
        <tr>
            <td><input type="text" name="key[]" style="width: 100px;" placeholder="请输入参数名称"></td>
            <td><input type="text" name="value[]" placeholder="请输入参数值"></td>
        </tr>
        <tr>
            <td colspan="2"> <input type="submit" value="提交"></td>
        </tr>
    </table>
</form>
<hr>
<iframe src="/index.php/Home/Api/submit" frameborder="0" width="100%" height="500" name="apiIframe"></iframe>
</body>
</html>