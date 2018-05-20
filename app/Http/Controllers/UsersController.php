<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * 用户列表
     * @DateTime 2018-05-20
     * @return   [type]     [description]
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * 删除资源
     * @DateTime 2018-05-20
     * @param    User       $user [description]
     * @return   [type]           [description]
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    /**
     * 注册页面
     * @DateTime 2018-05-20
     * @return   [type]     [description]
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * 展示用户信息
     * @DateTime 2018-05-19
     * @param    User       $user 用户信息
     * @return   [type]           [description]
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 注册用户
     * @DateTime 2018-05-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        // Auth::login($user);
        // session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        // return redirect()->route('users.show', [$user]);
        $this->sendEmailComfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
    }

    public function sendEmailComfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'zhi2niyige@vip.qq.com';
        $name = 'ATian';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function($message) use ($from, $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * 编辑页面
     * @DateTime 2018-05-19
     * @param    Request    $request [description]
     * @return   [type]              [description]
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        $userInfo = $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('info', '修改成功~');
        return redirect()->route('users.show', $user->id);
    }

    /**
     * 激活邮箱
     * @DateTime 2018-05-20
     * @param    [type]     $token [description]
     * @return   [type]            [description]
     */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
