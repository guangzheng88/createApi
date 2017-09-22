<?php
/**
 * 手动生成文档注释
 */
namespace Home\Controller;
use Think\Controller;
class HandApiController extends Controller {
    public function index()
    {
        $this->display();
    }
    /**
     * 生成文档注释
     */
    public function submit()
    {
        $post = I('post.');
       // $text = "<?php\n";
        $text .= "/**\n";
        //$text .= " * 以下为文档注释 用于生成apidoc ".API_AUTHOR.' '.date('Y-m-d H:i:s')."\n";
        //$text .= " *\n";
        $text .= " * @api {".$post['api_method']."} ".$post['api_url']." ".$post['api_title']."\n";
        $text .= " * @apiDescription ".$post['apiDescription']."\n";
        $text .= " * @apiGroup ".$post['apiGroup']."\n";
        //将斜杠转换成下划线
        $apiName = str_replace('/', '_', $post['api_url']).'_'.$post['api_method'];
        $text .= " * @apiName ".$apiName."\n";
        foreach ($post['key'] as $k => $v)
        {
            if ($v == '') continue;
            $text .= " * @apiParam {String} ".$v." ".$post['value'][$k]."\n";
        }
        if($post['arrKey'] != '')
        {
            $dataParams .= ' {<br>'."\n";
            //遍历数组子参数
            foreach ($post['arrParamKey'] as $key=>$val)
            {
                if($val != '')
                {
                    if(($key+1) != count($post['arrParamKey']))
                    {
                        $dataParams .= ' "'.$val.'" : "'.$post['arrParamValue'][$key].'",<br>'."\n";
                    }else
                    {
                        $dataParams .= ' "'.$val.'" : "'.$post['arrParamValue'][$key].'"<br>'."\n";
                    }
                }
            }
            $dataParams .= ' }'."\n";
            $text .= " * @apiParam {Array} ".$post['arrKey']."\n";
            $text .= $dataParams;
        }
        //$text .= " * @apiSuccess {Int} state 单元测试标识(成功)\n";
        //$text .= " * @apiSuccess {Int} status 接口是否调用成功的标识 1 为 成功\n";
        $text .= " * @apiSuccessExample {Object} 成功的响应:\n";
        $text .= " * ".$post['apiSuccessExample']."\n";
        //$text .= " * @apiError (Error  200) {Int} state !=1,接口调用失败\n";
        foreach ($post['errorkey'] as $key=>$val)
        {
            if ($val == '') continue;
            $text .= " * @apiError (Error  200) {Int} ".$val." ".$post['errorvalue'][$key]."\n";
        }
        $text .= " * @apiErrorExample {Object} 失败的响应，例如:\n";
        $text .= " * HTTP/1.1 200 OK\n";
        // $text .= " * object(stdClass)#1 (3) {\n";
        // $text .= '     ["status"]=> int(0)'."\n";
        // $text .= '     ["state"]=> int(0)'."\n";
        // $text .= '     ["error"]=> "参数错误"'."\n";
        // $text .= '   }'."\n";
        $text .= $post['apiErrorExample']."\n";
        //$text .= " * @apiSampleRequest off\n";
        $text .= " */";
        echo '<textarea style="width:800px;height:600px;">';
        echo $text;
        echo '</textarea>';
    }
}