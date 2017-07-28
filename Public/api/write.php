<?php
include_once '../../constants.php';
header("Content-type:text/html;charset=utf-8");
echo '<a href="index.html">返回首页</a><br>';
/**
 * bll接口需要生成文档注释
 * 因接口数量众多，特使用此办法看是否可以简写下注释
 * 2017-07-04
 */
//去空格
foreach($_POST as $key=>$val)
{
    if(!is_array($val))
    {
        if(!empty($val)) $data[$key] = trim($val);
    }else
    {
        foreach ($val as $k=>$v)
        {
            if(!empty($v)) $data[$key][$k] = trim($v);
        }
    }
}
$filename = $data['apiName'] ? trim($data['apiName']) : exit('filename error ');
// var_dump($data);
/********************************************************************************************/
$filename = APIDOC_PATH.$filename.'.php';
$text = "<?php\n";
$text .= "/**\n";
$text .= " * 以下为文档注释 用于生成apidoc ".$data['author'].' '.date('Y-m-d H:i:s')."\n";
$text .= " *\n";
$text .= " * @api {".$data['method']."} ".$data['apiUrl']." ".$data['apiChName']."\n";
$text .= " * @apiDescription ".$data['apiDescription']."\n";
$text .= " * @apiGroup ".$data['apiGroup']."\n";
$text .= " * @apiName ".$data['apiName']."\n";
$text .= " * @apiParam {String} token ".$data['token']."\n";
if(isset($data['allParam']) && !empty($data['allParam']) && count($data['allParam']) >= 1)
{
    foreach($data['allParam'] as $val)
    {
        $p1 = explode('`', $val);
        $text .= " * @apiParam {String} ".trim($p1[0])." ".trim($p1[1])."\n";
    }
}else
{
    $text .= " * @apiParam {Array} ".$data['apiParamName']." ".nl2br($data['apiParam'])."\n";
}
foreach ($data['apiDefineSuccess'] as $val)
{
    $text .= " * @apiSuccess ".$val."\n";
}
$text .= " * @apiSuccessExample {Object} 成功的响应:\n";
$text .= " *".$data['success']."\n";
foreach ($data['apiDefineError'] as $val)
{
    $text .= " * @apiError ".$val."\n";
}
$text .= " * @apiErrorExample {Object} 失败的响应，例如:\n";
$text .= " *HTTP/1.1 200 OK\n";
$text .= " *".$data['error']."\n";
if($data['method'] != 'get' && $data['method'] != 'post')
{
    $text .= " * @apiSampleRequest off\n";
}
$text .= " */";
$myfile = fopen($filename,"w") or die("Unable to open file!");
fwrite($myfile,$text);
fclose($myfile);
/********************************************************************************************/
echo '<textarea style="width:1300px;height:800px;">';
$text = str_replace('<?php', '', $text);
echo $text;
echo '</textarea>';
echo '<br><a href="index.html">返回首页</a>';