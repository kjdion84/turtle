<?php

namespace Kjdion84\Turtle\Controllers;

use App\Http\Controllers\Controller;
use Kjdion84\Turtle\Traits\Shellshock;

class RoleController extends Controller
{
    use Shellshock;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:Browse Roles')->only(['index', 'indexDatatable']);
        $this->middleware('can:Add Roles')->only(['addModal', 'add']);
        $this->middleware('can:Edit Roles')->only(['editModal', 'edit']);
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
        return datatables()->of(app(config('turtle.models.role'))->get())->toJson();
    }

    // show add role modal
    public function addModal()
    {
        $group_permissions = app(config('turtle.models.permission'))->orderBy('group', 'asc')->orderBy('id', 'asc')->get()->groupBy('group');

        return view('turtle::roles.add', compact('group_permissions'));
    }

    // add role
    public function add()
    {
        $this->shellshock(request(), [
            'name' => 'required|unique:roles',
        ]);

        $role = app(config('turtle.models.role'))->create(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Added Role', $role);

        return response()->json([
            'flash' => ['success', 'Role added!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    // show edit role modal
    public function editModal($id)
    {
        $role = app(config('turtle.models.role'))->findOrFail($id);
        $group_permissions = app(config('turtle.models.permission'))->orderBy('group', 'asc')->orderBy('id', 'asc')->get()->groupBy('group');

        return view('turtle::roles.edit', compact('role', 'group_permissions'));
    }

    // edit role
    public function edit($id)
    {
        $this->shellshock(request(), [
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = app(config('turtle.models.role'))->findOrFail($id);
        $role->update(request()->all());
        $role->permissions()->sync(request()->input('permissions'));

        activity('Edited Role', $role);

        return response()->json([
            'flash' => ['success', 'Role edited!'],
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