<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Builder\Function_;

class UserController extends Controller
{
   public function apiIndex() {
        return User::with('role')->get()
            ->map(fn($u)=>[
                'id'=>$u->id,
                'name'=>$u->name,
                'email'=>$u->email,
                'role'=>$u->role->name
            ]);
    }
    public function index()
    {
        return view('users');
    }
    public Function RolesData() {
        return Role::all();
    }
    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id'
        ]);

        $data['password'] = bcrypt($data['password']);

        User::create($data);

        return response()->json(['ok' => true]);
    }

    public function changeRole(Request $r, User $user)
    {
        $data = $r->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update($data);

        return response()->json(['ok' => true]);
    }

}
