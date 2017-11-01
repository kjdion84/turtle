// bread_model_variable routes
Route::get('bread_model_variables', 'bread_controller_routes@index')->name('bread_model_variables');
Route::get('bread_model_variables/datatable', 'bread_controller_routes@indexDatatable')->name('bread_model_variables.datatable');
Route::get('bread_model_variables/add', 'bread_controller_routes@addModal')->name('bread_model_variables.add');
Route::post('bread_model_variables/add', 'bread_controller_routes@add');
Route::get('bread_model_variables/edit/{id}', 'bread_controller_routes@editModal')->name('bread_model_variables.edit');
Route::patch('bread_model_variables/edit/{id}', 'bread_controller_routes@edit');
Route::delete('bread_model_variables/delete', 'bread_controller_routes@delete')->name('bread_model_variables.delete');