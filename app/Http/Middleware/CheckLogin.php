<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\Model\WechatUserModel;
use Illuminate\Support\Facades\Redis;
class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $session_id=Session::getId();//获取sessionid
        // echo $session_id;
        $userInfo=session('userInfo');
        // redis::flushall();清除redis
        // dd($userInfo);
        $user_id=$userInfo['user_id'];//取出用户id
        //根据用户id查询数据库信息
        $userModel=WechatUserModel::where(['user_id'=>$user_id])->first();
        //防止多端登录
        if($session_id!=$userModel['session_id']){
            return redirect('log')->withErrors(['该账户已在其他地址登录']);
            // echo "该账户已在其他地址登录";exit;
        }
        //超过数据库时间
        if(time()>$userModel['log_time']){
            session()->flush(); //清除session ，重新登录
            return redirect('log');
        }
        //一直操作，更新登录时间
        WechatUserModel::where(['user_id'=>$user_id])->update([
            "log_time"=>time()+300  //登录时间
        ]);
        return $next($request);
    }
}
