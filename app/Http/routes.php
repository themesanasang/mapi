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

	// Authentication routes...
    Route::get('/', 'Auth\AuthController@getLogin');
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@getLogout');

	// Authentication facebook routes...
	Route::get('auth/facebook', 'Auth\AuthController@redirectToProvider');
	Route::get('auth/facebook/callback', 'Auth\AuthController@handleProviderCallback');

	// Registration routes...
	Route::get('auth/listuser', 'Auth\AuthController@getListUser');
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');
	Route::get('auth/edituser/{id}', 'Auth\AuthController@getEditUser');
	Route::post('auth/updateuser', 'Auth\AuthController@postUpdateUser');
	Route::post('auth/deleteuser', 'Auth\AuthController@postDeleteUser');


    //Home page
    Route::get('/systems', 'AdminController@index');
    Route::get('/home', 'ContentController@index');


    //Dashboard admin
    Route::get('/getchart01', 'AdminController@getchart01');


    // Mikrotik routes...
    Route::get('routes/pageroom/{id}', 'MikrotikController@getPageRoom');
    Route::get('routes/editpageroom/{id1}/{id2}', 'MikrotikController@getEditPageRoom');
    Route::post('routes/addroom', 'MikrotikController@postAddRoom');
    Route::post('routes/editroom', 'MikrotikController@postEditRoom');
    Route::get('routes/deleteroom/{id1}/{id2}', 'MikrotikController@getDeleteRoom');

    Route::get('routes/hotspot/userprofile/{id}', 'MikrotikController@getUserProfile');
    Route::get('routes/hotspot/adduserprofile/{id}', 'MikrotikController@getAddUserProfile');
    Route::post('routes/hotspot/adduserprofile', 'MikrotikController@postAddUserProfile');
    Route::get('routes/hotspot/edituserprofile/{id1}/{id2}', 'MikrotikController@getEditUserProfile');
    Route::post('routes/hotspot/edituserprofile', 'MikrotikController@postEditUserProfile');
    Route::get('routes/hotspot/deleteuserprofile/{id1}/{id2}', 'MikrotikController@getDeleteUserProfile');

    Route::get('routes/hotspot/usernet/{id}', 'MikrotikController@getUserNet');
    Route::get('routes/hotspot/usernet/{id}/{id2}', 'MikrotikController@getUserNetSearch');
    Route::get('routes/hotspot/addusernet/{id}', 'MikrotikController@getAddUserNet');
    Route::post('routes/hotspot/addusernet', 'MikrotikController@postAddUserNet');
    Route::get('routes/hotspot/editusernet/{id1}/{id2}', 'MikrotikController@getEditUserNet');
    Route::post('routes/hotspot/editusernet', 'MikrotikController@postEditUserNet');
    Route::get('routes/hotspot/deleteusernet/{id1}/{id2}', 'MikrotikController@getDeleteUserNet');
    Route::post('routes/hotspot/usernet/userchkdelete', 'MikrotikController@postDeleteUserNet');
    Route::get('routes/hotspot/addfileusernet/{id}', 'MikrotikController@getAddFileUserNet');
    Route::post('routes/hotspot/addfileusernet', 'MikrotikController@postAddFileUserNet');
    Route::get('routes/hotspot/addcardusernet/{id}', 'MikrotikController@getAddCardUserNet');
    Route::post('routes/hotspot/addcardusernet', 'MikrotikController@postAddCardUserNet');

    Route::get('routes/hotspot/moveusernetroom/{id}', 'MikrotikController@getMoveRoomUserNet');
    Route::get('routes/hotspot/moveusernetroom/getuserroom/{id}/{id2}', 'MikrotikController@getUserRoom');
    Route::get('routes/hotspot/moveusernetroom/getuserroom_new/{id}/{id2}', 'MikrotikController@getUserRoomNew');
    Route::post('routes/hotspot/moveusernetroom/{id}', 'MikrotikController@postMoveRoomUserNet');

    Route::post('routes/deleteroutes', 'MikrotikController@postDeleteRoutes');
    Route::resource('routes','MikrotikController');

});



