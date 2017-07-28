<?php
/**
 * 公共函数
 */
/**
 * [CURL模拟post请求]
 * @param  [string] $url         [请求路径]
 * @param  [array] $fields      [请求参数] array( 'data' => '111' );
 * @param  array  $extraheader [header头部的重写]
 * @return [type]              [返回的数据]
 */
function doPost($url, $fields='', $extraheader = array()){
    if(is_array($fields))
    {
        $fields = http_build_query($fields);    //将数据进行URL-encode转换
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $extraheader);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    $output = curl_exec($ch);
    if(curl_errno($ch) != 0) $output = 'Curl error: ' . curl_error($ch);//curl错误信息
    curl_close($ch);
    return $output;
}
/**
 * [CURL模拟get请求]
 * @param  [string] $url         [请求路径]
 * @param  [array] $fields      [请求参数] array( 'data' => '111' );
 * @param  array  $extraheader [header头部的重写]
 * @return [type]              [返回的数据]
 */
function doGet($url, $fields='',$extraheader = array()){
    $getString = http_build_query($fields);  //将数据进行URL-encode转换
    $url = $url.'?'.$getString;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $extraheader);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回:
    $output = curl_exec($ch);
    if(curl_errno($ch) != 0) $output = 'Curl error: ' . curl_error($ch);//curl错误信息
    curl_close($ch);
    return $output;
}
/**
 * 递归创建
 */
function mkDirs($dir){
    if(!is_dir($dir)){
        if(!mkDirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,0555)){
            return false;
        }
        chmod($dir,0777);
    }
    return true;
}