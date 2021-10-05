<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Freshwork\ChileanBundle\Facades\Rut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        if(!Auth::user()->hasPermissionTo('user_list')) { abort(403); }

        // Se muestra la vista con el listado de usuarios
        return view('admin.user.index')->with([
            'users' => User::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        if(!Auth::user()->hasPermissionTo('user_create')) { abort(403); }

        return view('admin.user.create')->with([
            'permissions' => Permission::orderBy('name')->get(),
            'user' => new User()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     */
    public function store(UserRequest $request) {
        if(!Auth::user()->hasPermissionTo('user_create')) { abort(403); }

        $validatedRequest = $request->validated();

        $user = User::create([
            'name' => $validatedRequest['name'],
            'username' => $validatedRequest['username'],
            'rut' => Rut::parse($validatedRequest['rut'])->normalize(),
            'email' => $validatedRequest['email'],
            'password' => Hash::make($validatedRequest['password'])
        ]);

        $user->syncPermissions($request->input('permissions'));

        Session::flash('message', $validatedRequest['name'].' creado con éxito');
        Session::flash('alert-type', 'success');

        return redirect()->route('admin.user.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  User  $user
     */
    public function edit(User $user) {
        if(!Auth::user()->hasPermissionTo('user_update')) { abort(403); }

        $permissions = Permission::orderBy('name')->get();

        $data = array(
            'permissions' => $permissions,
            'user' => $user
        );

        return view('admin.user.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest  $request
     * @param  User  $user
     */
    public function update(UserRequest $request, User $user) {
        if(!Auth::user()->hasPermissionTo('user_update')) { abort(403); }

        $validatedRequest = $request->validated();

        $user->update([
            'name' => $validatedRequest['name'],
            'username' => $validatedRequest['username'],
            'rut' => Rut::parse($validatedRequest['rut'])->normalize(),
            'email' => $validatedRequest['email']
        ]);

        if( $validatedRequest['password'] !== null
            || $validatedRequest['password'] !== ''){

            $user->update([
                'password' => $validatedRequest['password']
            ]);
        }

        $user->save();
        $user->syncPermissions($request->input('permissions'));

        Session::flash('message', $validatedRequest['name'].' actualizado con éxito');
        Session::flash('alert-type', 'success');

        return redirect()->route('admin.user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Requests\UserRequest  $user
     */
    public function destroy(User $user) {
        if(!Auth::user()->hasPermissionTo('user_destroy')) { abort(403); }

        $name = $user->name;

        if(Auth::user()->rut == $user->rut){
            Session::flash('message', 'No te puedes eliminar a ti mismo');
            Session::flash('alert-type', 'error');

            return redirect()->route('admin.user.index');
        }

        if(count($user->appointments) != 0){
            Session::flash('message', $name. ' tiene reuniones asociadas, no es posible eliminar');
            Session::flash('alert-type', 'error');

            return redirect()->route('admin.user.index');
        }

        $user->delete();

        Session::flash('message', $name.' eliminado con éxito');
        Session::flash('alert-type', 'success');

        return redirect()->route('admin.user.index');
    }

    public function permissionShift(User $user, Request $request) {
        // Verificación de permiso
        if(!Auth::user()->hasPermissionTo('user_update')) { abort(403); }

        $permission = $request->permission;

        if($permission == null){ abort(400); }

        if($permission == 'user_login' && Auth::user()->rut == $user->rut){
            Session::flash('message', 'No se puede revocar el inicio de sesión al usuario autenticado');
            Session::flash('alert-type', 'error');

            return redirect()->route('admin.user.index');
        }

        if($user->hasPermissionTo($permission)){
            $user->revokePermissionTo($permission);
            Session::flash('message', 'Se ha REVOCADO el permiso "'. $permission. '" al usuario '. $user->name);
            Session::flash('alert-type', 'success');
        } else {
            $user->givePermissionTo($permission);
            Session::flash('message', 'Se ha OTORGADO el permiso "'. $permission. '" al usuario '. $user->name);
            Session::flash('alert-type', 'success');
        }

        return redirect()->route('admin.user.index');
    }
}
