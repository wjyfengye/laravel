<?php
namespace App\Tools;
use Illuminate\Support\Facades\Cache;
class Wechat{
    //公众测试号信息
    const appID="wxc2b62abdef4c789b";    //常量
    const appsecret="da95073579f6918d1bec41c2ac3966d9";

    /**
     * 回复文本消息
     */
    public static function responseText($msg,$postObj){
     echo "<xml>
            <ToUserName><![CDATA[".$postObj->FromUserName."]]></ToUserName>
            <FromUserName><![CDATA[".$postObj->ToUserName."]]></FromUserName>
            <CreateTime><![CDATA[".Time()."]]></CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[".$msg."]]></Content>
          </xml>";exit;
    }
    
    /**
     * 回复图片信息
     */
    public static function responseImg($postObj,$MediaId){
        echo "<xml>
                <ToUserName><![CDATA[".$postObj->FromUserName."]]></ToUserName>
                <FromUserName><![CDATA[".$postObj->ToUserName."]]></FromUserName>
                <CreateTime>".time()."</CreateTime>
                <MsgType><![CDATA[image]]></MsgType>
                <Image>
                <MediaId><![CDATA[".$MediaId."]]></MediaId>
                </Image>
            </xml>";exit;
    }

    /**
     *  获取access_token令牌
     */
    public static function getToken(){
        // $access_token = "";
        // Cache::flush('access_token'); //清除缓存
        $access_token=Cache::get("access_token");
        if(empty($access_token)){
             //调用接口   access_token  是公众号全局唯一接口调用凭据
            $data="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".Self::appID."&secret=".Self::appsecret;
	        //获取用户信息
	        $data=file_get_contents($data);//得到的是json串
	        $data=json_decode($data,true);//将json串转成数组
	        // var_dump($data);exit;
            $access_token=$data['access_token'];
            //存入缓存
            Cache::put("access_token",$access_token,600);
        }
        return $access_token;
    }

    /**
     *  获取用户信息
     */
    public static function getUserInfo($openid){
        $access_Token= self::getToken();
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_Token."&openid=".$openid."&lang=zh_CN";
        $userInfo=file_get_contents($url);
        $userInfo=json_decode($userInfo,true);
        return $userInfo;
    }

    /**
     * 获取一周天气预报  借款单
     */
    public static function getWeather($city){
        //调用K780天气接口
        $url="http://api.k780.com/?app=weather.future&weaid={$city}&&appkey=46450&sign=af74aeea77b69fe71f80eda34f717d28&format=json";
        $data=file_get_contents($url);//读取文件  也可读取地址
        // dd($data);
        $data=json_decode($data,true);  //读取的是json格式，将json转成数组
        // dd($data);
        $msg="";
        foreach($data['result'] as $k=>$v){
            $msg .=$v['days'].$v['week'].$v['citynm'].$v['weather'].$v['temperature']."\n";
        }
        return $msg;
    }

    /**
     * 上传素材接口
     */
    public static function uploadMidea($media_format,$img){
        //获取token
       $access_token= self::getToken();
       //调用上传临时素材接口地址
       $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$media_format}";
   
      //上传图片
      $postData['media']=new \CURLFile($img);//图片信息上传到微信
      //post方式提交
      $res=Curl::curlPost($url,$postData);
      //dd($res);exit;
      $res= json_decode($res,true);
      //dd($res);exit;
      $wechat_media_id=$res['media_id'];
      return $wechat_media_id;
    }

    /**
     * 渠道接口
     */

     /**
     * 网页授权获取用户openid
     * @return [type] [description]
     */
    public static function getOpenid(){
        //先去session里取openid 
        $openid = session('openid');
        //var_dump($openid);die;
        if(!empty($openid)){
            return $openid;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::appID."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::appID."&secret=".self::appsecret."&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            //获取到openid之后  存储到session当中
            session(['openid'=>$openid]);
            return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
        }   
    }

    /**
     * 网页授权获取用户基本信息
     * @return [type] [description]
     */
    public static function getOpenidByUserInfo()
    {
        //先去session里取openid 
        $userInfo = session('userInfo');
        //var_dump($openid);die;
        if(!empty($userInfo)){
            return $userInfo;
        }
        //微信授权成功后 跳转咱们配置的地址 （回调地址）带一个code参数
        $code = request()->input('code');
        if(empty($code)){
            //没有授权 跳转到微信服务器进行授权
            $host = $_SERVER['HTTP_HOST'];  //域名
            $uri = $_SERVER['REQUEST_URI']; //路由参数
            $redirect_uri = urlencode("http://".$host.$uri);  // ?code=xx
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".self::appid."&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
            header("location:".$url);die;
        }else{
            //通过code换取网页授权access_token
            $url =  "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".self::appid."&secret=".self::secret."&code={$code}&grant_type=authorization_code";
            $data = file_get_contents($url);
            $data = json_decode($data,true);
            $openid = $data['openid'];
            $access_token = $data['access_token'];
            //获取到openid之后  存储到session当中
            //session(['openid'=>$openid]);
            //return $openid;
            //如果是非静默授权 再通过openid  access_token获取用户信息
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
            $userInfo = file_get_contents($url);
            $userInfo = json_decode($userInfo,true);
            //返回用户信息
            session(['userInfo'=>$userInfo]);
            return $userInfo;
        }   
    }
}


