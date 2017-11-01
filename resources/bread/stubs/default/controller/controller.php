<?php

/* bread_controller_namespace */

/* bread_model_use */
use App\Http\Controllers\Controller;
use Kjdion84\Turtle\Traits\Shellshock;

class bread_controller_class extends Controller
{
    use Shellshock;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:Browse bread_model_strings')->only(['index', 'indexDatatable']);
        $this->middleware('can:Add bread_model_strings')->only(['addModal', 'add']);
        $this->middleware('can:Edit bread_model_strings')->only(['editModal', 'edit']);
        $this->middleware('can:Delete bread_model_strings')->only('delete');
    }

    public function index()
    {
        return view('bread_controller_viewbread_model_variables.index');
    }

    public function indexDatatable()
    {
        return datatables()->of(bread_model_class::query())->toJson();
    }

    public function addModal()
    {
        return view('bread_controller_viewbread_model_variables.add');
    }

    public function add()
    {
        $this->shellshock(request(), [
            /* bread_rule_add */
        ]);

        $bread_model_variable = bread_model_class::create(request()->all());

        activity('Added bread_model_string', $bread_model_variable);

        return response()->json([
            'flash' => ['success', 'bread_model_string added!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    public function editModal($id)
    {
        $bread_model_variable = bread_model_class::findOrFail($id);

        return view('bread_controller_viewbread_model_variables.edit', compact('bread_model_variable'));
    }

    public function edit($id)
    {
        $this->shellshock(request(), [
            /* bread_rule_edit */
        ]);

        $bread_model_variable = bread_model_class::findOrFail($id);
        $bread_model_variable->update(request()->all());

        activity('Edited bread_model_string', $bread_model_variable);

        return response()->json([
            'flash' => ['success', 'bread_model_string edited!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    public function delete()
    {
        $this->shellshock(request(), [
            'id' => 'required',
        ]);

        $bread_model_variable = bread_model_class::findOrFail(request()->input('id'));
        $bread_model_variable->delete();

        activity('Deleted bread_model_string', $bread_model_variable);

        return response()->json([
            'flash' => ['success', 'bread_model_string deleted!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }
}