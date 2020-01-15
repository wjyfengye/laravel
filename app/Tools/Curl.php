<?php
namespace App\Tools;
class Curl{
       /**
     * 封装的curlGet请求
     */
    public static function curlGet($url){
        //初始化: curl_init
        $ch=curl_init();
        //设置: curl_setopt
        curl_setopt($ch,CURLOPT_URL,$url);//请求地址
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//返回数据格式
        //RETURN 返回   TRANSFER格式   1是以数据的方式返回  不设置1，就会将数据直接抛给浏览器输出
        
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//对认证证书来源的检查   如果是https网站 时设置
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//从证书中检测SLL加密算法是否存在  如果是https网站 时设置
       
        //执行  curl_exec
        $result=curl_exec($ch);
        //关闭释放  curl_close
        curl_close($ch);
        return $result;
    }

    /**
     * 封装的curlPost请求
     */
    public static function curlPost($url,$postData){
        //初始化: curl_init
        $ch=curl_init();
        //设置: curl_setopt
        curl_setopt($ch,CURLOPT_URL,$url);//请求地址
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//返回数据格式
        //RETURN 返回   TRANSFER格式   1是以数据的方式返回  不设置1，就会将数据直接抛给浏览器输出
        
        curl_setopt($ch, CURLOPT_POST, 1);//提交post方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//post提交数据

        //访问https网站时，关闭ssl验证
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//对认证证书来源的检查   如果是https网站 时设置
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//从证书中检测SLL加密算法是否存在  如果是https网站 时设置
    
        //执行  curl_exec
        $result=curl_exec($ch);
        //关闭释放  curl_close
        curl_close($ch);
        return $result;
    }
}
?>