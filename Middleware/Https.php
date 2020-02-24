<?php

namespace App\Http\Middleware;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use App\Http\Models\Areas;
use URL;
use Closure;
class Https
{
    /*
     * 用来判断地区链接并且实例化地区类传入对应参数 从而 beijing.xxxxx.cn  || hebei.xxxxx.cn
     * */
    public function handle($request, Closure $next)
    {
        $url = $_SERVER['HTTP_HOST'];//获取当前 xxx.xxx.cn
//        echo 'http://www.xxxxx.cn'.$_SERVER["REQUEST_URI"];die;
        if($_SERVER["REQUEST_URI"] == '/index.php' || $_SERVER["REQUEST_URI"] == '/index.php/index' || $_SERVER["REQUEST_URI"] == '/index'){
            return response()->redirectTo('http://www.xxxxx.cn',301); //重定向首页
        }
        if($url == 'xxxxx.cn'){
            return response()->redirectTo('http://www.xxxxx.cn'.$_SERVER["REQUEST_URI"],301);  //重定向首页
        }else{
            $res = Areas::GetUrl($where = ['url'=>$url]);  //数据库查询是否存在xxx.xxx.cn   （www.xxxxx.cn 不在数据库中）
            if(empty($res)){
                return $next($request);
            }else{
                if(trim($request->path(),'/') != ''){
                    return $next($request);
                }else{
                    /**
                     * 实例化地区控制器 调用地区页方法
                     */
                    $Controller = new \App\Http\Controllers\Home\AddressController();
                    $result = $Controller->address($data = ['id'=>$res['id'],'shengname'=>$res['shengname']]);
                    return response()->view('home/address/address',['data'=>$result['data'],'count'=>$result['count'],'prev'=>$result['prev']]);
                }
            }
        }

    }

}
