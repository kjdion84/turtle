<?php

/* crud_controller_namespace */

/* crud_model_use */
use App\Http\Controllers\Controller;

class crud_controller_class extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:View crud_model_strings')->only(['index', 'indexDatatable']);
        $this->middleware('can:Create crud_model_strings')->only(['createModal', 'create']);
        $this->middleware('can:Update crud_model_strings')->only(['updateModal', 'update']);
        $this->middleware('can:Delete crud_model_strings')->only('delete');
    }

    public function index()
    {
        return view('crud_controller_viewcrud_model_variables.index');
    }

    public function indexDatatable()
    {
        return datatables()->of(crud_model_class::query())->toJson();
    }

    public function createModal()
    {
        return view('crud_controller_viewcrud_model_variables.create');
    }

    public function create()
    {
        $this->shellshock(request(), [
            /* crud_rule_create */
        ]);

        $crud_model_variable = crud_model_class::create(request()->all());

        activity('Created crud_model_string', $crud_model_variable);

        return response()->json([
            'flash' => ['success', 'crud_model_string created!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    public function updateModal($id)
    {
        $crud_model_variable = crud_model_class::findOrFail($id);

        return view('crud_controller_viewcrud_model_variables.update', compact('crud_model_variable'));
    }

    public function update($id)
    {
        $this->shellshock(request(), [
            /* crud_rule_update */
        ]);

        $crud_model_variable = crud_model_class::findOrFail($id);
        $crud_model_variable->update(request()->all());

        activity('Updated crud_model_string', $crud_model_variable);

        return response()->json([
            'flash' => ['success', 'crud_model_string updated!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }

    public function delete()
    {
        $this->shellshock(request(), [
            'id' => 'required',
        ]);

        $crud_model_variable = crud_model_class::findOrFail(request()->input('id'));
        $crud_model_variable->delete();

        activity('Deleted crud_model_string', $crud_model_variable);

        return response()->json([
            'flash' => ['success', 'crud_model_string deleted!'],
            'dismiss_modal' => true,
            'reload_datatables' => true,
        ]);
    }
}