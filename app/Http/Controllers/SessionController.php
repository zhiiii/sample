<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionController extends Controller
{
    /**
     * 登陆页面
     * @DateTime 2018-05-19
     * @return   [type]     [description]
     */
    public function create()
    {
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
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth::user()]);
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }

    public function destory()
    {
        Auth::logout();
        session()->flash('success', '退出成功~');
        return redirect()->route('login');
    }
}
