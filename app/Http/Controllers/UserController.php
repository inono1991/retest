<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->whereIn('ur.role_id', $roleIds)->count(DB::raw('DISTINCT '. DB::getTablePrefix().'u.id'));
        $users = new LengthAwarePaginator($users, $totalItems, $limit, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
        return view('user/index', ['users' => $users, 'user_roles' => $userRoles, 'current_role' => session('role')]);
    }

    public function info($id)
    {
        $info = User::with('roles', 'permissions')->where('id', $id);
        dd($info);
        return view('user/info', ['info' => $info]);
    }

    public function edit(Request $request)
    {
        $id = $request->input('id');
        if (!$id || !User::where('id', $id)->exists()) {
            return view('user/eddit', ['code' => 404, 'message' => '该用户不存在']);
        }
    }

    public function delete($id)
    {

    }

}
