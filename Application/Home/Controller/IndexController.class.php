<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        // echo testReturn();
        // echo BLL_PATH;
        $this->display();
    }
    public function left()
    {
        $this->display();
    }
    public function main()
    {
        $this->display();
    }
}