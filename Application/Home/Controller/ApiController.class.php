<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller
{
    /**
     * 输入接口信息页面
     */
    public function index()
    {
        $param = I('get.');
        $apiDir = trim($param['apiDir']);
        $apiName = trim($param['apiName']);
        $apiFunction = trim($param['apiFunction']);
        $method = strtolower(trim($param['method']));
        $apiUrl = BLL_URL;
        if(!empty($apiDir)) $apiUrl .= $apiDir;
        if(!empty($apiName)) $apiUrl .= '/'.$apiName;
        if(!empty($apiFunction)) $apiUrl .= '/'.$apiFunction;
        if(empty($method)) $method = 'get';
        $param = json_decode(urldecode(I('get.param')));
        $this->assign('param',$param);
        $this->assign('apiUrl',$apiUrl);
        $this->assign('method',$method);
        $this->display();
    }

    /**
     * 处理接口返回值页面
     */
    public function submit()
    {
        //接收参数并去空格
        $post = I('post.');
        foreach ($post as $key => $val)
        {
            if(is_array($val))
            {
                //组装一维参数
                foreach ($post['key'] as $k => $v)
                {
                    if(!empty($v)) $param[$v] = trim($post['value'][$k]);
                }
            }else
            {
                if(!empty($val)) $data[$key] = trim($val);
            }
        }
        //组装二维数组参数
        $arrKey = trim($post['arrKey']);
        if(!empty($arrKey))
        {
            foreach ($post['arrParamKey'] as $key=>$val)
            {
                $val = trim($val);
                if(!empty($val)) $param[$arrKey][$val] = trim($post['arrParamValue'][$key]);
            }
        }
        //请求url
        $url = $data['bll_url'];
        if($url == '') exit('');
        if(strtolower($data['method']) == 'post')
        {
            //执行post请求
            $res = doPost($url,$param);
        }else if (strtolower($data['method']) == 'delete')
        {
            $res = doDelete($url,$param);
        }else if (strtolower($data['method']) == 'put')
        {
            $res = doPut($url,$param);
        }else
        {
            //执行get请求
            $res = doGet($url,$param);
        }
        echo '<h3>接口原始返回数据：</h3>';
        echo '<textarea style="width:800px;height:200px;">'.$res.'</textarea>';
        echo '<h3>json_decode格式化后的数据为：</h3>';
        $returnData = json_decode($res);
        if(is_array($returnData) || is_object($returnData))
        {
            echo '<textarea style="width:800px;height:600px;">';
            dump($returnData);
            echo '</textarea>';
        }else
        {
            echo $res;
        }
    }
    /**
     * 生成文档注释接口
     */
    public function apiAnnotation()
    {
        $post = I('post.');
        $$unitPath = $post['apiDir']."/".$post['apiName']."/".$post['apiName'].ucfirst($post['method']);
        $parameter = $this->getUnitContent($$unitPath);
       // $text = "<?php\n";
        $text .= "/**\n";
        $text .= " * 以下为文档注释 用于生成apidoc ".API_AUTHOR.' '.date('Y-m-d H:i:s')."\n";
        $text .= " *\n";
        $text .= " * @api {".$post['method']."} ".$post['apiDir']."/".$post['apiName']."/".$post['apiFunction']." ".$parameter['apiChName']."\n";
        $text .= " * @apiDescription ".$parameter['apiDescription']."\n";
        $text .= " * @apiGroup ".$post['apiDir']."\n";
        $text .= " * @apiName ".$post['apiName']."_".$post['apiFunction']."_".$post['method']."\n";
        foreach ($post['key'] as $k => $v)
        {
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
            $text .= " * @apiParam {Array} ".$data['arrKey']."\n";
            $text .= $dataParams;
        }
        $text .= " * @apiSuccess {Int} state 单元测试标识(成功)\n";
        $text .= " * @apiSuccess {Int} status 接口是否调用成功的标识 1 为 成功\n";

        $text .= " * @apiSuccessExample {Object} 成功的响应:\n";
        $text .= " * ".$parameter['success']."\n";
        $text .= " * @apiError (Error  200) {Int} state !=1,接口调用失败\n";
        if($parameter['error'])
        {
            foreach ($parameter['error'] as $key=>$val)
            {
                $text .= " * @apiError (Error  200) {Int} state-".($key+2)." ".$val."\n";
            }
        }else
        {
            $text .= " * @apiError (Error  200) {Int} state-2 参数错误\n";
        }
        $text .= " * @apiErrorExample {Object} 失败的响应，例如:\n";
        $text .= " * HTTP/1.1 200 OK\n";
        $text .= " * object(stdClass)#1 (3) {\n";
        $text .= '     ["status"]=> int(0)'."\n";
        $text .= '     ["state"]=> int(0)'."\n";
        $text .= '     ["error"]=> "参数错误"'."\n";
        $text .= '   }'."\n";
        if($post['method'] != 'get' && $post['method'] != 'post')
        {
            $text .= " * @apiSampleRequest off\n";
        }
        $text .= " */";
        echo '<textarea style="width:800px;height:600px;">';
        echo $text;
        echo '</textarea>';
    }

    /**
     * 查找单元测试内容
     */
    public function getUnitContent($unitPath = '')
    {
        //获取单元测试内容
        $unitPath = BLL_PATH.'tests/controllers/'.$unitPath.'Test.php';
        $content = file_get_contents($unitPath);
        if($content == false) return false;

        // 接口中文注释
        $array = explode("\n",$content);
        $apiChName = mb_ereg_replace('<类描述>', '', $array[2]);
        $data['apiChName'] = trim(str_replace('*', '', $apiChName));
        //接口详细描述
        $apiDescription = mb_ereg_replace('<详细描述>', '', $array[3]);
        $data['apiDescription'] = trim(str_replace('*', '', $apiDescription));

        //获取成功响应值
        $start = strpos($content,'接口成功返回值示例 start');
        $stop = strpos($content,'接口成功返回值示例 end');
        $successReps = substr($content, $start,$stop);
        $arraySucces = explode("\n",$successReps);
        $count = count($arraySucces);
        $endKey = $count-1;
        $secendKey = $count-2;
        unset($arraySucces[0],$arraySucces[$endKey-3],$arraySucces[$endKey-2],$arraySucces[$endKey-1]);
        $successObjec = implode("\n",$arraySucces);
        $data['success'] = $successObjec;

        //失败返回
        $errorContent = getNeedBetween($content,'失败返回状态说明','@dataProvider');
        $errorArr = explode("\n",$errorContent);
        foreach ($errorArr as $val)
        {
            $pattern = '/("error":")(.+?)(")/';
             preg_match( $pattern, $val, $matches);
             $error = $matches[2];
             if($error)
             {
                //preg_match('/[\x7f-\xff]+/', $val, $matches );
                //$error = $matches[0];
                $data['error'][] = $error;
             }
             unset($error);
        }
        return $data;
    }
}