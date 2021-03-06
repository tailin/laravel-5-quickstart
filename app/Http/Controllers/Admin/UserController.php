<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\DataTables\UserDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserDataTable $dataTable
     * @return mixed
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.user.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $data = [
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ];

        $user = $this->userRepository->create($data);
        //用户创建失败
        if (! $user) {
            flash('用户创建失败')->error()->important();

            return;
        }
        //如果为管理员
        if (request('is_administrator') == 1) {
            $user->assignRole('administrator');
        }
        flash('用户创建成功')->success()->important();

        return redirect()->route('admin.user.index');
    }

    /**
     * @param User $user
     * @return $this
     */
    public function show(User $user)
    {
        return view('admin.user.show')
            ->with('user', $user);
    }

    /**
     * @param User $user
     * @return $this
     */
    public function edit(User $user)
    {
        return view('admin.user.edit')
            ->with('user', $user);
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $password = request('password');
        //设置了新密码
        if ($password) {
            $this->userRepository->update(bcrypt($password), $user->id);
        }
        //如果设置用户为管理员
        if (request('is_administrator') == 1 && ! $user->hasRole('administrator')) {
            $user->assignRole('administrator');
        }
        //非管理员
        if (request('is_administrator') != 1) {
            $user->removeRole('administrator');
        }
        flash('用户信息修改成功')->success()->important();

        return redirect()->back();
    }

    /**
     * @param User $user
     */
    public function destroy(User $user)
    {
        $this->userRepository->delete($user->id);
        flash('用户删除成功')->success()->important();

        return redirect()->back();
    }
}
