<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

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
        Auth::login($user);
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
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
}
