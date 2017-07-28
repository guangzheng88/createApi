<?php
include_once '../../constants.php';
header("Content-type:text/html;charset=utf-8");
$fileName = isset($_GET['fileName']) ? trim($_GET['fileName']) : '';
if(!empty($fileName)) :
    $apiGroup = explode('/', $fileName);
    $testFileName = '';
    if(is_array($apiGroup) && count($apiGroup) > 1)
    {
        $fileName = $apiGroup[0].'/'.lcfirst($apiGroup[1]);
        //单元测试目录
        $testFileName = $apiGroup[0].'_test/'.lcfirst($apiGroup[1]).'_test';
    }
    //获取文件内容
    $filename = BLL_PATH.'application/controllers/'.$fileName.'.php';
    if(file_exists($filename)) $content = file_get_contents($filename);
    if(!isset($content) || !$content)
    {
        $filename = JAVA_PATH.'src/main/java/com/corefire/servlet/'.ucfirst($fileName).'.java';
        $content = file_get_contents($filename);
        $isJavaApi = true;
    }else
    {
        $isJavaApi = false;
    }
    if(!$content) exit('<h1>error filename or no content !</h1>');
    $pattern = '/(class )(.+?)( extends)/';
    preg_match( $pattern, $content, $matches );
    $apiName = trim(lcfirst($matches[2]));//接口名称
    if($isJavaApi) $apiName = ucfirst($apiName);
    preg_match('/[\x7f-\xff]+/', $content,$matche);
    $apiDescription = $matche[0];//接口描述
    //寻找参数
    $paramPattern = '/(\$params\[\')(.+?)(\'\])/';
    preg_match_all($paramPattern, $content,$matcheParam);
    $apiParamName = 'params';
    if(!isset($matcheParam[2]) || empty($matcheParam[2]))
    {
        $paramPattern = '/(\$param\[\')(.+?)(\'\])/';
        preg_match_all($paramPattern, $content,$matcheParam);
        $apiParamName = 'param';
    }
    if(!isset($matcheParam[2]) || empty($matcheParam[2]))
    {
        $paramPattern = '/(\$data\[\')(.+?)(\'\])/';
        preg_match_all($paramPattern, $content,$matcheParam);
        $apiParamName = 'data';
    }
    $params = array_unique($matcheParam[2]);
// var_dump($params);exit;
    //判断是get请求还是post请求
    $isGet = strpos($content, "_get(");
    if($isGet === false)
    {
        //判断put
        $isPut = strpos($content, "_put(");
        if($isPut === false)
        {
            //判断post
            $isPost = strpos($content, "_post(");
            if($isPost === false)
            {
                $methodType = 'delete';
            }else
            {
                $methodType = 'post';
            }
        }else
        {
            $methodType = 'put';
        }
    }else
    {
        $methodType = 'get';
    }
   //$methodType = ($isGet === false) ? 'post' : 'get';
   //获取单元测试内容
   $testFile = BLL_PATH.'application/controllers/'.$testFileName.'.php';
   if(file_exists($testFile))
   {
        $testContent = file_get_contents($testFile);
        //寻找参数
        if(empty($params))
        {
            $paramPattern = '/(params\[\')(.+?)(\'\])/';
            preg_match_all($paramPattern, $testContent,$matcheParam);
            $apiParamName = 'params';
            if(!isset($matcheParam[2]) || empty($matcheParam[2]))
            {
                $paramPattern = '/(data\[\')(.+?)(\'\])/';
                preg_match_all($paramPattern, $testContent,$matcheParam);
                $apiParamName = 'data';
            }
            $params = array_unique($matcheParam[2]);
        }
        //寻找注释
        $zhushiPattern = '/(state\,\')(.+?)(\'\))/';
        preg_match_all($zhushiPattern, $testContent,$matcheZhushi);
        $findZhuShi = array();
        $a1 = array();
        if(is_array($matcheZhushi[2]))
        {
            foreach ($matcheZhushi[2] as $key => $val)
            {
                $a1 = explode(',', $val);
                foreach($a1 as $ak=>$a)
                {
                    $a10 = trim($a,"'");
                    $a10 = trim($a10,'"');
                    $a1[$ak] = $a10;
                }
                if($a1[0] != 1) $findZhuShi[$a1[0]] = $a1[2];
            }
        }
   }
?>
<div style="background:#f3f3f3;">
    <?php   //echo nl2br($content);//换行显示 ?>
    <a href="index.html">返回首页</a>
</div><hr>
<style type="text/css">
    textarea{width:400px;height:150px;}
    .mainTable input{width:400px;height:38px;}
    a{text-decoration: none;}
    .addSuccess,.addError,.addParam{margin-left:20px;font-size: 18px;color:white;font-weight: bold;border:1px solid #333;padding:0px 10px;background: orange;}
</style>
<script type="text/javascript" src="jquery-1.7.2.js"></script>
<form style="background: #abcdef;" action="write.php" autocomplete="off" method="post">
    <table class="mainTable">
         <tr>
            <td>
                <input type="text" name="apiUrl" value="<?php echo $fileName;?>">
            </td>
            <td> 接口地址 </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="method" value="<?php echo $methodType;?>">
            </td>
            <td> 请求方式，get or post </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="apiChName" value="<?php  echo $apiDescription;?>">
            </td>
            <td> 接口中文名称 </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="apiDescription" value="<?php  echo $apiDescription;?>">
            </td>
            <td> 接口描述 </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="apiGroup" value="<?php echo $apiGroup[0];?>">
            </td>
            <td> 分组，例：ljorder </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="apiName" value="<?php echo $apiName.'_'.$methodType;?>">
            </td>
            <td> 接口名称 </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="token" value="接口密钥(接口名称+请求方式+关键字)" placeholder="">
            </td>
            <td> token参数 <a href="javascript:;" class="addParam">+</a></td>
        </tr>
        <tr class="addAllParam">
            <td>
                <textarea name="apiParam">
{
    <?php
        foreach ($params as $key=>$val)
        {
            if((count($params)-1) != $key)
            {
                echo '"'.$val.'":"'.$val."\",\n";
            }else
            {
                echo '"'.$val.'":"'.$val."\"\n";
            }
        }
    ?>
}
                </textarea>
            </td>
            <td> 参数 </td>
        </tr>
        <tr>
            <td>
                <textarea name="success">
object(stdClass)#1 (2) {
   ["state"]=> int(1)
   ["status"]=> int(1)
}
       </textarea>
            </td>
            <td> 成功响应值 </td>
        </tr>
        <tr>
            <td>
                <textarea name="error">
object(stdClass)#1 (2) {
   ["state"]=> int(0)
   ["status"]=> int(0)
   ["error"]=> "参数错误"
}
                </textarea>
            </td>
            <td> 失败响应值 </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="apiDefineSuccess[]" value="{Int} state 单元测试标识(成功)">
            </td>
            <td> apiSuccess 成功返回值1 </td>
        </tr>
        <tr class="addSuccessTr">
            <td>
                <input type="text" name="apiDefineSuccess[]" value="{Int} status 接口是否调用成功的标识 1 为 成功">
            </td>
            <td> apiSuccess 成功返回值2 <a href="javascript:;" class="addSuccess">+</a></td>
        </tr>
        <tr class="beforAddSuccess">
            <td style="border-top: 2px solid red;">
                <input type="text" name="apiDefineError[]" value="(Error  200) {Int} state !=1,接口调用失败">
            </td>
            <td style="border-top: 2px solid red;"> apiError 失败返回值1 <a href="javascript:;" class="addError">+</a></td>
        </tr>
        <?php
            if(!empty($findZhuShi) && isset($findZhuShi[2]))
            {
                foreach ($findZhuShi as $k=>$v)
                {
                    echo '
                        <tr class="addErrorTr">
                            <td>
                                <input type="text" name="apiDefineError[]" value="(Error  200) {Int} state-'.$k.' '.$v.'">
                            </td>
                            <td> apiError 失败返回值'.($k).' </td>
                        </tr>
                    ';
                }
            }else
            {
                echo '
                <tr class="addErrorTr">
                    <td>
                        <input type="text" name="apiDefineError[]" value="(Error  200) {Int} state-2 参数错误">
                    </td>
                    <td> apiError 失败返回值2 </td>
                </tr>
                ';
            }
        ?>
        <!-- <tr class="addErrorTr">
            <td>
                <input type="text" name="apiDefineError[]" value="(Error  200) {Int} state-2 参数错误">
            </td>
            <td> apiError 失败返回值 <a href="javascript:;" class="addError">+</a></td>
        </tr> -->
        <!-- <tr>
            <td>
                <input type="text" name="apiDefineError[]" value="(Error  200) {Int} status-3 该合同有退款记录">
            </td>
            <td> apiDefine 失败返回值 </td>
        </tr> -->
        <tr class="beforAddError">
            <td> <input type="text" name="author" value="<?php echo API_AUTHOR;?>"> </td>
            <td> author </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;"><input type="submit" value="生成文档"></td>
        </tr>
    </table>
    <input type="hidden" name="apiParamName" value="<?php echo $apiParamName;?>">
</form>
<script type="text/javascript">
    var n = 1;
    <?php
            if(!empty($findZhuShi) && isset($findZhuShi[2]))
            {
                echo 'var e = '.(count($findZhuShi)+2).';';
            }else
            {
                echo 'var e = 3;';
            }
    ?>
    var s = 3;
    $(function(){
        $('.addSuccess').click(function(){
            var html = '<tr> <td> <input type="text" name="apiDefineSuccess[]" value=""> </td> <td> apiSuccess 成功返回值'+s+' </td> </tr>';
            $('.beforAddSuccess').before(html);
            s++;
        });
        $('.addError').click(function(){
            var html = '<tr> <td> <input type="text" name="apiDefineError[]" value=""> </td> <td> apiError 失败返回值'+e+' </td> </tr>';
            $('.beforAddError').before(html);
            e++;
        });
        $('.addParam').click(function(){
            var html = '<tr> <td> <input type="text" name="allParam[]" value="" placeholder="以`区分参数与注释"> </td> <td> 参数'+n+'</td> </tr>';
            $('.addAllParam').before(html);
            n++;
        })
    });
    function createToken()
    {
        var c = $("[name='className']").val();
        var f = $("[name='functionName']").val();
        var g = $("[name='group']").val();
        $.ajax({
            type: 'GET',
            url: 'createApi.php' ,
            data: {c:c,f:f,g:g,type:'md5'},
            async : true, //默认为true 异步请求
            cache:false,
            success:function(data){
                $('.token').html(data);
            },error:function(){
                console.log('ajax error');
            }
        });
    }
    function request()
    {
        var url = '<?php echo $apiGroup[0].'/'.$apiName;?>';
        var token = $("[name='tokenReq']").val();
        var params = $("#apiParamRequest").val();
        $.ajax({
            type: 'POST',
            url: 'index.php' ,
            data: {token:token,params:params,url:url},
            async : true, //默认为true 异步请求
            cache:false,
            success:function(data){
                $('#response').val(data);
            },error:function(){
                console.log('ajax error');
            }
        });
    }
</script>
<div>
    <h3>请求示例</h3>
    <table>
        <tr>
            <td>接口地址：</td>
            <td colspan="4">http://bll.api.loc/index.php/<?php echo $apiGroup[0].'/'.$apiName;?></td>
        </tr>
        <tr>
            <td>获取token</td>
            <td>
                <input type="text" name="className" value="<?php echo $matches[2];?>">
                <input type="text" name="functionName" value="index_<?php echo $methodType;?>">
                <input type="text" name="group" value="<?php echo $apiGroup[0];?>">
            </td>
            <td><a href="javascript:createToken();">生成token</a></td>
            <td style="background:orange;padding: 5px 10px;" class="token"></td>
        </tr>
        <tr>
            <td>token:</td>
            <td colspan="4"><input type="text" name="tokenReq"></td>
        </tr>
        <tr>
            <td>params:</td>
            <td colspan="3">
            <textarea id="apiParamRequest">
{
<?php
    foreach ($params as $key=>$val)
    {
        if((count($params)-1) != $key)
        {
            echo '"'.$val.'":"'.$val."\",\n";
        }else
        {
            echo '"'.$val.'":"'.$val."\"\n";
        }
    }
?>
}
            </textarea>
            </td>
            <td><a href="javascript:request();">发送请求</a></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="4">
                <textarea id="response"></textarea>
            </td>
        </tr>
    </table>
</div>
<div style="text-align: right;">
    <a href="index.html">返回首页</a>
</div>
<?php endif;?>

<?php
if(isset($_GET['type']) &&  $_GET['type'] == 'md5')
{
    $c = trim($_GET['c']);
    $f = trim($_GET['f']);
    $g = trim($_GET['g']);
    echo md5($c.$f.$g);exit;
}
?>

