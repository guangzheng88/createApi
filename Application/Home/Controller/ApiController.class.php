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
}