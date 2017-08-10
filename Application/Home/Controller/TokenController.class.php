<?php
namespace Home\Controller;
use Think\Controller;
class TokenController extends Controller {
    /**
     * 获取token页面
     * @date 2017-08-10 11:01:38
     */
    public function index(){
        $this->display();
    }
    /**
     * 生成token
     */
    public function submit()
    {
        $api = I('get.api');
        $arr = explode('index.php/', $api);
        $apiArr = explode('/', $arr[1]);
        $filename = BLL_PATH.'application/controllers/'.$apiArr[0].'/'.$apiArr[1].'.php';
        $content = file_get_contents($filename);
        if(empty($content)) exit('no api or api error');
        if(!isset($apiArr[2]))
        {
            $apiArr[2] = 'index';
        }
        $pattern = '/(__FUNCTION__\.\')(.+?)(\'\))/';
        preg_match_all($pattern, $content,$matche);
        if(!isset($matche[2]) || empty($matche[2]))
        {
            $pattern = '/(__FUNCTION__\.\")(.+?)(\"\))/';
            preg_match_all($pattern, $content,$matche);
        }
        //获取关键字
        $key = $matche[2][0];
        $method = trim(I('get.method'));
        $md5 = ucfirst($apiArr[1]).$apiArr[2].'_'.$method.$key;
        echo '<span style="font-size:6px;color:#999;">'.$md5.'</span>';
        $token = md5($md5);
        echo '<h3>'.$token.'</h3>';
    }
    /**
     * 生成自定义token
     */
    public function diyToken()
    {
        $apiName = trim(I('get.apiName'));
        $apiFunction = trim(I('get.apiFunction'));
        $key = trim(I('get.key'));
        $md5 = ucfirst($apiName).$apiFunction.$key;
        echo '<span style="font-size:6px;color:#999;">'.$md5.'</span>';
        $token = md5($md5);
        echo '<h3>'.$token.'</h3>';
    }
}