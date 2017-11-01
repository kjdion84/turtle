<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Kjdion84\Turtle\Traits\Shellshock;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use Shellshock;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:Browse Users')->only(['index', 'indexDatatable']);
        $this->middleware('can:Add Users')->only(['addModal', 'add']);
        $this->middleware('can:Edit Users')->only(['editModal', 'edit', 'passwordModal', 'password']);
        $this->middleware('can:Delete Users')->only('delete');
        $this->middleware('can:Browse Activities')->only('activity', 'activityDatatable');
        $this->middleware('can:Read Activities')->only('activityDataModal');
    }

    // users index with table
    public function index()
    {
        return view('turtle::users.index');
    }

    // users index datatable
    public function indexDatatable()
    {
        $datatable = datatables()->of(app(config('turtle.models.user'))->with('roles')->get());
        $datatable->editColumn('roles', function ($user) {
            return $user->roles->sortBy('name')->pluck('name')->implode(', ');
        });

        return $datatable->toJson();
    }

    // show add user modal
    public function addModal()
    {
        $roles = app(config('turtle.models.role'))->get()->sortBy('name');

        return view('turtle::users.add', compact('roles'));
    }

    // add user
    public function add()
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'timezone' => 'required|in:' . implode(',', timezone_identifiers_list()),
        ]);

        request()->merge(['password' => Hash::make(request()->input('password'))]);
        $user = app(config('turtle.models.user'))->create(request()->all());
        $user->roles()->sync(request()->input('roles'));

        activity('Added User', $user);

        return response()->json([
            'flash' => ['success', 'User added!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show edit user modal
    public function editModal($id)
    {
        $user = app(config('turtle.models.user'))->findOrFail($id);
        $roles = app(config('turtle.models.role'))->get()->sortBy('name');

        return view('turtle::users.edit', compact('user', 'roles'));
    }

    // edit user
    public function edit($id)
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'timezone' => 'required|in:' . implode(',', timezone_identifiers_list()),
        ]);

        $user = app(config('turtle.models.user'))->findOrFail($id);
        $user->update(request()->all());
        $user->roles()->sync(request()->input('roles'));

        activity('Edited User', $user);

        return response()->json([
            'flash' => ['success', 'User edited!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show change user password modal
    public function passwordModal($id)
    {
        $user = app(config('turtle.models.user'))->findOrFail($id);

        return view('turtle::users.password', compact('user'));
    }

    // change user password
    public function password($id)
    {
        $this->shellshock(request(), [
            'password' => 'required|confirmed',
        ]);

        $user = app(config('turtle.models.user'))->findOrFail($id);
        $user->update(['password' => Hash::make(request()->input('password'))]);

        activity('Changed User Password', $user);

        return response()->json([
            'flash' => ['success', 'User password changed!'],
            'dismiss_modal' => true,
        ]);
    }

    // delete user
    public function delete()
    {
        $this->shellshock(request(), [
            'id' => 'required',
        ]);

        $user = app(config('turtle.models.user'))->findOrFail(request()->input('id'));
        $user->delete();

        activity('Deleted User', $user);

        return response()->json([
            'flash' => ['success', 'User deleted!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // user activity with table
    public function activity($id)
    {
        $user = app(config('turtle.models.user'))->findOrFail($id);

        return view('turtle::users.activity', compact('user'));
    }

    // user activity datatable
    public function activityDatatable($id)
    {
        return datatables()->of(app(config('turtle.models.activity'))->where('user_id', $id)->get())->toJson();
    }

    // show user activity data modal
    public function activityDataModal($id)
    {
        $activity = app(config('turtle.models.activity'))->findOrFail($id);
        $user = app(config('turtle.models.user'))->find($activity->user_id);
        $model = $activity->model_class ? app($activity->model_class)->find($activity->model_id) : null;

        return view('turtle::users.activity-data', compact('activity', 'user', 'model'));
    }
}