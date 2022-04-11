<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('tasks');
});

Auth::routes();


Route::resource('tasks', 'TaskController', [
   'only' => [
       'index', 'store',
   ]
]);
Route::post('/tasks/update', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');