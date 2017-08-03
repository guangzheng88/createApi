<?php
namespace Home\Controller;
use Think\Controller;
class PhpunitController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        header("Content-type:text/html;charset=utf-8");
    }
    //错误信息
    private $error = '';
    //接口请求方式
    private $method = 'get';
    //接口目录
    private $apiDir = '';
    //接口名称
    private $apiName = '';
    //方法名称
    private $apiFunction = '';
    //test文件名称
    private $testFile = '';
    //接口文件绝对路径
    private $apiFilePath = '';
    //可能有的参数
    private $apiParameter = '';
    /**
     * 初始化页面
     */
    public function index()
    {
        $this->display();
    }
    /**
     * 创建phpunit文件
     */
    public function createUnit()
    {
        //接收请求方式
        $this->method = ucfirst(trim(I('get.method')));
        //方法名称
        $this->apiFunction = (trim(I('get.function')) == 'index') ? '' : trim(I('get.function'));
        //接收文件地址
        $fileName = trim(I('get.fileName'));
        //拆分地址成数组，将首字母小写
        $fileArr = explode('/', $fileName);
        if(is_array($fileArr) && count($fileArr) > 1)
        {
            $this->apiDir = lcfirst($fileArr[0]);
            $this->apiName = lcfirst($fileArr[1]);
            //重新拼装文件名称
            $fileName = BLL_PATH.'application/controllers/'.$this->apiDir.'/'.$this->apiName.'.php';
        }else
        {
            exit('文件名错误，请输入controllers下的带目录不带后缀的文件名称，例：ljorder/checkFinalSubmit');
        }
        //判断文件是否存在
        if(file_exists($fileName))
        {
            $this->apiFilePath = $fileName;
            //创建文件夹并获取test文件名称
            $createFile = $this->createDirAndTestFile();
            if($createFile['state'] == 1)
            {
                //写入文件
                $content = $this->getContent();
                if($content == 1)
                {
                    //文件写入成功，拼接xml配置内容
                    $xmlConfig = '<file>../../../tests/controllers/'.$this->apiDir.'/'.$this->apiName.'/'.$this->apiName.$this->method.'Test.php</file>';
                    $this->redirect('home/api/index',array(
                            //'file'=>urlencode($xmlConfig),
                            'apiDir' => $this->apiDir,
                            'apiName' => $this->apiName,
                            'apiFunction' => $this->apiFunction,
                            'method' => $this->method,
                            'param' => urlencode(json_encode($this->apiParameter))
                        )
                    );
                }
            }else
            {
                $this->error = $createFile;
            }
        }else
        {
            $this->error = '接口文件不存在';
        }
        $this->assign('error',$this->error);
        echo '<h1> ╰_╯ '.$this->error.' ╰_╯</h1>';
    }
    /**
     * 递归创建单元测试目录
     */
    public function createDirAndTestFile()
    {
        //拼装文件名称
        $dir = BLL_PATH.'/tests/controllers/'.$this->apiDir.'/'.$this->apiName;
        $this->testFile = BLL_PATH.'tests/controllers/'.$this->apiDir.'/'.$this->apiName.'/'.$this->apiName.$this->method.'Test.php';
        $bool = mkDirs($dir);
        if($bool != true) return '创建目录失败（'.$dir.'），请查看是否有权限';
        return array('state'=>1,'path'=>$filename);
    }
    /**
     * 拼接文件内容
     */
    public function getContent()
    {
        if(file_exists($this->testFile))
        {
            if($_GET['cover'] != 1)
            {
                $this->assign('testFile',$this->apiDir.'/'.$this->apiName.'/'.$this->apiName.$this->method.'Test.php');
                $this->display("getContent");exit;
            }
        }
        //获取可能的参数
        $parameter = $this->getParameter();
        $text = "<?php\n";
        $text .= "/**\n";
        $text .= " * <类描述>\n";
        $text .= " * <详细描述>\n";
        $text .= " * <接口地址> ".BLL_URL.$this->apiDir."/".$this->apiName."/".$this->apiFunction."\n";
        $text .= " * @author ".API_AUTHOR."\n";
        $text .= " * @date ".date('Y-m-d H:i:s')."\n";
        $text .= " */\n";
        $text .= "class ".$this->apiName.$this->method."Test extends CI_TestCase\n";
        $text .= "{\n";
        $text .= "    /**\n";
        $text .= "     * ====================== 测试接口部分 ======================\n";
        $text .= "     * 被测方法名称:".$this->apiFunction."_".lcfirst($this->method)."\n";
        $text .= "     * 被测方法描述:\n";
        $text .= "     * 被测方法成功返回返回:请参考成功返回值示例:\n";
        $text .= "     * 失败返回状态说明:\n";
        $text .= "     * {\"status\":0,\"error\":\"参数错误\",\"state\":0}\n";
        $text .= "     *\n";
        $text .= "     * @dataProvider giveGetData\n";
        $text .= "     */\n";
        $text .= "    public function testGet(\$param,\$state)\n";
        $text .= "    {\n";
        $text .= "        //调用初始化接口\n";
        foreach ($parameter as $pk =>$pv)
        {
            $textParam .= "                    '".$pv."' => '".$pk."',\n";
            if($pk != 0)
            {
                $paramText .= ',\''.$pv.'\'=>$param[\''.$pv.'\']';
            }else
            {
                $paramText .= '\''.$pv.'\'=>$param[\''.$pv.'\']';
            }
        }
        $text .= "        \$res = \$this->CI->rest_interface->".lcfirst($this->method)."('".$this->apiDir."/".$this->apiName."/".$this->apiFunction."',array(".$paramText."));\n";
        $text .= "        //获取错误提示信息\n";
        $text .= "        \$error = isset(\$res->error) ? \$res->error : '';\n";
        $text .= "        //获取接口返回状态\n";
        $text .= "        \$status = isset(\$res->status) ? \$res->status : '1';\n";
        $text .= "        //断言不同的参数,返回状态是否正确\n";
        $text .= "        \$this->assertEquals(\$state,\$status,\$error);\n";
        $text .= "    }\n";
        $text .= "    /**\n";
        $text .= "     * 数据供给器\n";
        $text .= "     */\n";
        $text .= "    public function giveGetData()\n";
        $text .= "    {\n";
        $text .= "        return array(\n";
        $text .= "            //情景1\n";
        $text .= "            array(\n";
        $text .= "                'param' => array(\n";
        $text .= $textParam;
        $text .= "                ),\n";
        $text .= "                'state' => 1\n";
        $text .= "                ),\n";
        $text .= "            //情景2\n";
        $text .= "            array(\n";
        $text .= "                'param' => array(\n";
        $text .= $textParam;
        $text .= "                ),\n";
        $text .= "                'state' => 0\n";
        $text .= "                ),\n";
        $text .= "            );\n";
        $text .= "    }\n";
        $text .= "/*\n";
        $text .= "  ---------------------------------------------- 接口成功返回值示例 start ----------------------------------------------\n";
        $text .= "\n";
        $text .= "  ---------------------------------------------- 接口成功返回值示例 end  ----------------------------------------------\n";
        $text .= "*/\n";
        $text .= "}";
        $myfile = fopen($this->testFile,"w") or die("Unable to open file! 写入文件失败！");
        fwrite($myfile,$text);
        fclose($myfile);
        chmod($this->testFile,0666);
        return 1;
    }
    /**
     * 获取参数
     */
    public function getParameter()
    {
        //获取接口文件内容
        $content = file_get_contents($this->apiFilePath);
        //拼装接口方法名称
        $method = strtolower($this->method);
        $funName = $this->apiFunction.'_'.$method;
        //获取方法出现的开始位置
        $start = strpos($content, $funName);
        //截取方法内容
        $content =substr($content, $start);
        //获取方法大概结束位置
        $stop = strpos($content, 'function ');
        if($stop != false) $content =substr($content,0,$stop);
        //正则查找可能的参数
        $m = $method.'(\'' ;
        $pattern = '/(\$this->'.$method.'\(\')(.+?)(\'\))/';
        preg_match_all($pattern, $content,$matche);
        if(!$matche[2] && !is_array($matche[2]))  $matche[2] = array('key'=>'value');
        $this->apiParameter = $matche[2];
        return $matche[2];
    }
}