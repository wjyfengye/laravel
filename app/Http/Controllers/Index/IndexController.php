<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\WechatUserModel;
use Illuminate\Support\Facades\Redis;
use Session;

class IndexController extends Common
{
    // public  $key='1904';
    // public  $iv='1904a1904a1904aa';
    // public function index(){

    //     $data=[
    //         "user_name"=>"胡汉三",
    //         "user_pwd"=>"123123"
    //     ];
    //     $encrypt=$this->_AesEncrypt($data);
    //     $decrypt=$this->_AesDecrypt($encrypt);
    //     echo $encrypt;
    //     echo "<br>";
    //     print_r($decrypt) ;exit;
    // }

    public function login()
    {
        $data=[
            "user_name"=>"胡汉三",
            "user_pwd"=>"123123"
        ];
        $url="http://api.laravel.com/login";
     
        $api_result=$this->curlPost($url,$data);
        print_r($api_result);
    }

    public function test(){
        $data_str=str_repeat("0123456789",15);
        \openssl_encrypt(
            $data_str,      
        ); 
        var_dump($data_str) ;
        // public_path();//助手函数  返回绝对路径
    }
    
    public function log(){
        return view('index.login');
    }  
    
    public function logDo(){
        $user_name=request()->input('user_name');
        $user_pwd=request()->input('user_pwd');
        //获取sessionid
        $session_id=Session::getId();
        $error_number='';
        $userData=WechatUserModel::where(['user_name'=>$user_name])->first();
        
        
        $locking_time=$userData['locking_time'];//最后一次错误，锁定时间
        if(!empty($userData)){
            if($userData['user_pwd']!=md5($user_pwd)){ 
                //第一次错误
                if($userData['error_number']==0){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$error_number=1
                    ]);
                    return back()->withErrors(['密码错误,还有2次机会']);
                } 
                //累加
                if($userData['error_number']==1){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$userData['error_number']+1
                    ]);
                    return back()->withErrors(['密码错误,还有1次机会']);
                }
                if($userData['error_number']==2){
                    WechatUserModel::where(['user_name'=>$user_name])->update([
                        "error_number"=>$userData['error_number']+1,
                        "locking_time"=>time()+7200 //错误时间+2小时
                    ]);
                    return back()->withErrors(['密码错误,账号被锁定']);
                }    
            }  
        }

        if(time()-$locking_time<7200){
            $mins=ceil(($locking_time-time())/60);
            return back()->withErrors(["账号锁定中".$mins."分钟后进行登录"]);
            // echo "账号锁定中".$mins."分钟后进行登录";exit;
        }

       WechatUserModel::where(['user_name'=>$user_name])->update([
            "error_number"=>0,
            'session_id'=>$session_id,
            "locking_time"=>0,
            "log_time"=>time()+300  //登录时间
        ]);
        
        session(['userInfo'=>$userData]);
        return redirect('list');
    }


    public function list(){
       
        echo "11111";
    }
}
