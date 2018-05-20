<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 登陆页面
     * @DateTime 2018-05-19
     * @return   [type]     [description]
     */
    public function create()
    {
        // if (Auth::check()) {
        //     session()->flash('warning', '您已经登陆~');
        //     return redirect()->route('users.show', Auth::user());
        // }
        return view('session.create');
    }

    /**
     * 验证登陆并跳转
     * @DateTime 2018-05-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function store(Request $request)
    {
        $credential = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if (Auth::attempt($credential, $request->has('remember'))) {
            if (Auth::user()->activated) {
                 session()->flash('success', '欢迎回来！');
                return redirect()->intended(route('users.show', [Auth::user()]));
            } else {
                Auth::logout();
                session()->flash('warning', "你的账号未激活，请检查邮箱中的注册邮件进行激活。");
                return redirect('/');
            }
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '退出成功~');
        return redirect()->route('login');
    }
}
