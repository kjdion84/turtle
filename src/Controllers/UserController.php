<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:View Users')->only(['index', 'indexDatatable']);
        $this->middleware('can:Create Users')->only(['createModal', 'create']);
        $this->middleware('can:Update Users')->only(['updateModal', 'update', 'passwordModal', 'password']);
        $this->middleware('can:Delete Users')->only('delete');
        $this->middleware('can:View Activities')->only('activity', 'activityDatatable', 'activityDataModal');
    }

    // users index with table
    public function index()
    {
        return view('turtle::users.index');
    }

    // users index datatable
    public function indexDatatable()
    {
        $datatable = datatables()->of(User::with('roles')->get());
        $datatable->editColumn('roles', function ($user) {
            return $user->roles->sortBy('name')->pluck('name')->implode(', ');
        });

        return $datatable->toJson();
    }

    // show create user modal
    public function createModal()
    {
        $roles = app(config('turtle.models.role'))->get()->sortBy('name');

        return view('turtle::users.create', compact('roles'));
    }

    // create user
    public function create()
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'timezone' => 'required|in:' . implode(',', timezone_identifiers_list()),
        ]);

        request()->merge(['password' => Hash::make(request()->input('password'))]);
        $user = User::create(request()->all());
        $user->roles()->sync(request()->input('roles'));

        activity('Created User', $user);

        return response()->json([
            'flash' => ['success', 'User created!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show update user profile modal
    public function updateModal($id)
    {
        $user = User::findOrFail($id);
        $roles = app(config('turtle.models.role'))->get()->sortBy('name');

        return view('turtle::users.update', compact('user', 'roles'));
    }

    // update user profile
    public function update($id)
    {
        $this->shellshock(request(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'timezone' => 'required|in:' . implode(',', timezone_identifiers_list()),
        ]);

        $user = User::findOrFail($id);
        $user->update(request()->all());
        $user->roles()->sync(request()->input('roles'));

        activity('Updated User Profile', $user);

        return response()->json([
            'flash' => ['success', 'User profile updated!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show change user password modal
    public function passwordModal($id)
    {
        $user = User::findOrFail($id);

        return view('turtle::users.password', compact('user'));
    }

    // change user password
    public function password($id)
    {
        $this->shellshock(request(), [
            'password' => 'required|confirmed',
        ]);

        $user = User::findOrFail($id);
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

        $user = User::findOrFail(request()->input('id'));
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
        $user = User::findOrFail($id);

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
        $user = User::find($activity->user_id);
        $model = $activity->model_class ? app($activity->model_class)->find($activity->model_id) : null;

        return view('turtle::users.activity-data', compact('activity', 'user', 'model'));
    }
}