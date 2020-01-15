<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WechatUserModel;
use App\Tools\Wechat;
use App\Tools\Curl;
use Illuminate\Support\Facades\Cache;
class LoginController extends Common
{
   public function wechat(){
        //获取token
        $token=Wechat::getToken();
        //二维码图片标识
       echo  $name=md5(uniqid());
        // dd($token);
        //调用带二维码参数
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token}";
        // dd($url);
        $data='{"expire_seconds": 3600, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$name.'"}}}';
        // dd($data);
        $data=Curl::curlPost($url,$data);
        //
        $img=json_decode($data,true);
        $ticket=$img['ticket'];
        // dd($ticket);
        $imgUrl="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        // dd($imgUrl);
        return view('index/wechat',['imgUrl'=>$imgUrl,'name'=>$name]);
   }

   public function login(){

   }

   public function index()
   {
      $echostr=request()->echostr;
      if(!empty($echostr)){
         echo $echostr;
      }
      $xmlData=file_get_contents("php://input");
      file_put_contents('1.txt',$xmlData);
      //将xml格式转成xml对象
      $xmlObj=simplexml_load_string($xmlData,'SimpleXMLElement',LIBXML_NOCDATA);
      //判断用户未关注过
      if($xmlObj->MsgType=="event"&&$xmlObj->Event=="subscribe"){
            //获取openid
            $openId=(string)$xmlObj->FromUserName;
            //获取二维码标识
            $EventKey=(string)$xmlObj->EventKey;
            $status=ltrim($EventKey,'qrscene_');
            if(!empty($status)){
               //带参数关注事件
               Cache::put($status,$openId,20);
               //回复文本消息
               echo $msg="正在扫描登录，耐心等待";
               Wechat::responseText($msg,$xmlObj);
            }
      }
      //判断用户关注过
      if($xmlObj->MsgType=="event"&&$xmlObj->Event=="SCAN"){
         //获取openid
         $openId=(string)$xmlObj->FromUserName;
         //获取二维码
         $status=(string)$xmlObj->EventKey;
         if(!empty($status)){
            //带参数关注事件
            Cache::put($status,$openId,20);
            //回复文本消息
            echo $status;
            echo $msg="已关注扫描登录，耐心等待";
            Wechat::responseText($msg,$xmlObj);
         }
      }
     
   }

   public function checkWechatLogin(){
      $name=request()->name;
      $openId=Cache::get($name);
      // dd($openId);
      if(!$openId){
          return json_encode(['font'=>'用户未登录','msg'=>2]);
      }
      return json_encode(['font'=>'用户已扫描','msg'=>1]);
   }
}
