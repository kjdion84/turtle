<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:View Roles')->only(['index', 'indexDatatable']);
        $this->middleware('can:Create Roles')->only(['createModal', 'create']);
        $this->middleware('can:Update Roles')->only(['updateModal', 'update']);
        $this->middleware('can:Delete Roles')->only('delete');
    }

    // roles index with table
    public function index()
    {
        return view('turtle::roles.index');
    }

    // roles index datatable
    public function indexDatatable()
    {
        return datatables()->of(app(config('turtle.models.role'))->query())->toJson();
    }

    // show create role modal
    public function createModal()
    {
        $group_permissions = app(config('turtle.models.permission'))->orderBy('group', 'asc')->orderBy('id', 'asc')->get()->groupBy('group');

        return view('turtle::roles.create', compact('group_permissions'));
    }

    // create role
    public function create()
    {
        $this->shellshock(request(), [
            'name' => 'required|unique:roles',
        ]);

        $role = app(config('turtle.models.role'))->create(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Created Role', $role);

        return response()->json([
            'flash' => ['success', 'Role created!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show update role modal
    public function updateModal($id)
    {
        $role = app(config('turtle.models.role'))->findOrFail($id);
        $group_permissions = app(config('turtle.models.permission'))->orderBy('group', 'asc')->orderBy('id', 'asc')->get()->groupBy('group');

        return view('turtle::roles.update', compact('role', 'group_permissions'));
    }

    // update role
    public function update($id)
    {
        $this->shellshock(request(), [
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = app(config('turtle.models.role'))->findOrFail($id);
        $role->update(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Updated Role', $role);

        return response()->json([
            'flash' => ['success', 'Role updated!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // delete role
    public function delete()
    {
        $this->shellshock(request(), [
            'id' => 'required',
        ]);

        $role = app(config('turtle.models.role'))->findOrFail(request()->input('id'));
        $role->delete();

        activity('Deleted Role', $role);

        return response()->json([
            'flash' => ['success', 'Role deleted!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }
}