<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/




/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

	Route::get('/', function () {
	    if(Auth::check()){
	    	if( Auth::user()->type == 'user' ){
	    		return view('content/index');
	    	}else{
	    		return view('admin/index');
	    	}
	    }else{
	        return view('auth/login');
	    }
	});

	// Authentication routes...
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@getLogout');

	// Registration routes...
	Route::get('auth/listuser', 'Auth\AuthController@getListUser');
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');
	Route::get('auth/edituser/{id}', 'Auth\AuthController@getEditUser');
	Route::post('auth/updateuser', 'Auth\AuthController@postUpdateUser');
	Route::post('auth/deleteuser', 'Auth\AuthController@postDeleteUser');


    Route::get('/systems', 'AdminController@index');
    Route::get('/home', 'ContentController@index');



    // Mikrotik routes...
    Route::get('routes/pageroom/{id}', 'MikrotikController@getPageRoom');
    Route::get('routes/editpageroom/{id1}/{id2}', 'MikrotikController@getEditPageRoom');
    Route::post('routes/addroom', 'MikrotikController@postAddRoom');
    Route::post('routes/editroom', 'MikrotikController@postEditRoom');
    Route::get('routes/deleteroom/{id1}/{id2}', 'MikrotikController@getDeleteRoom');
    Route::post('routes/deleteroutes', 'MikrotikController@postDeleteRoutes');
    Route::resource('routes','MikrotikController');

});



