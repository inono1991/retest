<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $order = $request->input('order', 'asc');
        $userRoles = DB::table('user_role as ur')->join('roles as r', 'r.id', '=', 'ur.role_id', 'left')
            ->where('ur.user_id', $user->id)->orderBy('r.level', 'asc')->pluck('name', 'id');
        if (!session('role')) {
            session(['role' => $userRoles->shift()]);
        }
        $userRoleLevel = Role::where('name', session('role'))->value('level');
        $roleIds = Role::where('level', '>', $userRoleLevel)->pluck('id')->toArray();
        $limit = 10;
        $page = $request->input('page', 1);
        $users = DB::table('users as u')->join('user_role as ur', 'u.id', '=', 'ur.user_id', 'left')
            ->whereIn('ur.role_id', $roleIds)->select('u.*')->distinct()->orderBy('u.id', $order)->skip($limit * ($page - 1))->take($limit)->get();
        $totalItems = DB::table('users as u')->join('user_role as ur', 'u.id', '=', 'ur.user_id', 'left')
            ->whereIn('ur.role_id', $roleIds)->count(DB::raw('DISTINCT ' . DB::getTablePrefix() . 'u.id'));
        $users = new LengthAwarePaginator($users, $totalItems, $limit, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
        return view('user/index', ['users' => $users, 'user_roles' => $userRoles]);
    }

    public function info($id)
    {
        if (! $id) {
            return response()->json(['code' => 204, 'message' => '参数不合法！']);
        }
        $info = User::with('roles')->where('id', $id)->first();
        // 当前管理员的角色 管理员没有该角色则不显示
        $userRoles = DB::table('user_role')->where('user_id', Auth::id())->pluck('role_id')->toArray();
        if (count($info->roles) > 0) {
            foreach ($info->roles as $role) {
                if (! in_array($role, $userRoles)) {
                    unset($role);
                }
                $role->permissioon = Role::with('permissions')->find($role->id);
            }
        }
        return response()->json(['code' => 0,'data'=> ['info' => $info]]);
    }

    //通过角色获取权限列表
    public function getPermissionsByRole(Request $request)
    {
        $userId = $request->input('user_id');
        $roleId = $request->input('role_id');
        if (!$roleId || !$userId) {
            return response()->json(['code' => 404, 'message' => '参数错误或者数据不存在！']);
        }
        $permissions = DB::table('permissions as p')
            ->join('user_permission as up', 'p.id', 'up.permission_id', 'left')
            ->where('up.user_id', $userId)->where('role_id', $roleId)->where('up.status', 1)->get();
        return response()->json(['code' => 0, 'data' => ['permissions' => $permissions]]);
    }

    public function edit(Request $request)
    {
        //参数是否合法
        $validator = Validator::make($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);
        if ($validator->fails()) {
            return redirect('users')
                ->withErrors($validator)
                ->withInput();
        }
        $id = $request->input('id');
        if (!$id || !User::where('id', $id)->exists()) {
            return view('user/edit', ['code' => 404, 'message' => '该用户不存在']);
        }
        $updateStatus = false;
        DB::transaction(function () use ($request,$id,&$updateStatus){
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->save();
            $roles = json_decode($request->input('roles', true));
            //先删除当前没有选择的角色
            DB::table('user_role')->whereNotIn('role_id', $roles)->delete();
            //然后添加新加的角色
            if ($roles) {
                foreach ($roles as $role) {
                    if (DB::table('user_role')->where('user_id', $id)->where('role_id', $role)->exists()) {
                        continue;
                    }
                    DB::table('user_role')->insert(['user_id' => $id, 'role_id' => $role]);
                    $permissions = DB::table('role_permission')->where('role_id', $role)->pluck('permission_id')->toArray();
                    foreach ($permissions as $permission) {
                        DB::table('user_permission')->insert(['user_id' => $id, 'role_id' => $role, 'permission_id' => $permission]);
                    }
                }
            }
            $updateStatus = true;
        });

        if ($updateStatus) {
            return view('users', ['code' => 0 ,'message' => '修改成功!']);
        } else {
            return view('users', ['code' => 500 ,'message' => '修改失败!']);
        }


    }

    public function delete($id)
    {

    }

}
