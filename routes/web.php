<?php

use Illuminate\Support\Facades\Route;

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



Route::get('/','HomeController@getPage')
->middleware('guest');

//Route::get('/','Auth\LoginController@showLoginForm');

Route::get('/notfound','NotfoundController@getPage')->name('notfound');

//admin

Route::group(['prefix' => 'admin'], function () {
    Auth::routes(/*['register' => false]*/['verify' => true]);//just adding the verification routes, i could add them normal way too...
	//Auth::routes();
});


// Registration Steps
Route::get('/regstep2', 'Regsteps\RegStepsController@secondStep')->name('regsteps.step2')
->middleware(['auth','verified']);
Route::get('/regstep3', 'Regsteps\RegStepsController@thirdStep')->name('regsteps.step3')
->middleware(['auth','verified']);

Route::post('/regstep2', 'Regsteps\RegStepsController@secondStepSubmit')->name('regsteps.step2.submit')
->middleware(['auth','verified']);
Route::post('/regstep3', 'Regsteps\RegStepsController@thirdStepSubmit')->name('regsteps.step3.submit')
->middleware(['auth','verified']);

Route::post('/ajax/suggestgenre', 'AjaxController@suggestGenre')->name('suggestgenre')
->middleware(['auth','verified']);
// Registration Steps


Route::get('/admin', 'Admin\HomeController@getPage')->name('admin.home')
->middleware(['auth','verified','reg_step_check']);



Route::get('/admin/profile', 'Admin\ProfileController@getPage')   
->middleware(['auth','verified','reg_step_check'])->name('profile.update');

Route::patch('/admin/profile', 'Admin\ProfileController@saveProfile')   
->middleware(['auth','verified','reg_step_check']);

Route::get('/admin/passwordchange', 'Admin\PasswordChangeController@getPage')   
->middleware(['auth','verified','reg_step_check'])->name('password.change');
Route::patch('/admin/passwordchange', 'Admin\PasswordChangeController@changePassword')   
->middleware(['auth','verified','reg_step_check']);

Route::post('/admin/sendhomeform', 'Admin\HomeController@sendHomeForm')->name('admin.sendhomeform')
->middleware(['auth','verified','reg_step_check']);


Route::get('/admin/spotifytracks', 'Admin\SpotifyTracksController@getPage')->name('spotifytracks')  
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor']);


Route::get('/admin/spotifyplaylists', 'Admin\SpotifyPlaylistsController@getPage')->name('spotifyplaylists')  
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,playlister']);

Route::post('/admin/ajax/addplaylists', 'Admin\AjaxController@addPlaylists') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,playlister']);

Route::post('/admin/ajax/removeplaylist', 'Admin\AjaxController@removePlaylist') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor']);


Route::get('/admin/spotifyaccounts', 'Admin\SpotifyAccountsController@getPage')->name('spotifyaccounts')  
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager,playlister']);

Route::get('/admin/addspotifyaccount', 'Admin\SpotifyAccountsController@grantSpotifyAccess') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager,playlister']);

Route::post('/admin/ajax/addstraightaccount', 'Admin\AjaxController@addStraightAccount') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager,playlister']);

Route::post('/admin/ajax/addsimpleartist', 'Admin\AjaxController@addSimpleArtist') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

Route::post('/admin/ajax/generateartistcode','Admin\AjaxController@generateArtistCode')
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

Route::post('/admin/ajax/changething', 'Admin\AjaxController@changeThing') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

Route::post('/admin/ajax/showpassword', 'Admin\AjaxController@showPassword') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

Route::post('/admin/ajax/changeartistpick', 'Admin\AjaxController@changeArtistPick') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

Route::post('/admin/ajax/getartistpicksimple', 'Admin\AjaxController@getArtistPickSimple') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);


Route::post('/admin/ajax/removemanager', 'Admin\AjaxController@removeManager') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor']);


Route::post('/admin/ajax/removeartist', 'Admin\AjaxController@removeArtist') 
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);



Route::get('/connectspotify', 'ConnectSpotifyController@getPage')->name('connectspotify');
Route::get('/grantspotifyaccess', 'ConnectSpotifyController@grantSpotifyAccess');


Route::get('/admin/tracksinfos', 'Admin\TracksInfosController@getPage')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/getinfoabouttrack', 'Admin\TracksInfosController@getInfoAboutTrack')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');


Route::get('/admin/statistics', 'Admin\StatisticsController@getPage')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/settings', 'Admin\SettingsController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

//Groups
Route::get('/admin/groups', 'Admin\GroupsController@getPage')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');
Route::post('/admin/ajax/addnewgroup', 'Admin\AjaxController@addNewGroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');
Route::post('/admin/ajax/removegroup', 'Admin\AjaxController@removeGroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');



Route::post('/admin/ajax/getitemsgroups', 'Admin\AjaxController@getItemsGroups')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/getmultipleitemsgroups', 'Admin\AjaxController@getMultipleItemsGroups')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/removefromgroup', 'Admin\AjaxController@removeFromGroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');


Route::post('/admin/ajax/suggestgroup', 'Admin\AjaxController@suggestGroup')->name('suggestgroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/addtogroup', 'Admin\AjaxController@addToGroup')->name('addtogroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/group/{groupid}', 'Admin\GroupPageController@getPage')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/addmultipletogroup', 'Admin\AjaxController@addMultipleToGroup')->name('addmultipletogroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::post('/admin/ajax/removemultiplefromgroup', 'Admin\AjaxController@removeMultipleFromGroup')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');
//Groups


Route::post('/admin/settings', 'Admin\SettingsController@updateSettings')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');


Route::get('/admin/downloadcsv', 'Admin\LocalDatabaseController@downloadTheResultset')    
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,assistant']);

Route::post('/admin/ajax/downloadartistresultset', 'Admin\AjaxController@downloadArtistResultset')    
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,assistant']);


Route::get('/admin/searchesinprogress', 'Admin\SearchesInProgressController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/claimedinprogress', 'Admin\ClaimedInProgressController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/search', 'Admin\SearchController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,assistant']);


Route::get('/admin/localdatabase', 'Admin\LocalDatabaseController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,assistant']);

Route::get('/admin/users', 'Admin\UsersController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/genres', 'Admin\GenresController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/contact', 'Admin\ContactController@getPage')    
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

Route::get('/admin/artist/{artistid}', 'Admin\ArtistPageController@getPage')
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,artistmanager']);

// Authentication Routes...
Route::get('/admin/login', [ 'as' => 'login', 'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('/admin/ajax/login','Auth\LoginController@login')->name('admin.login');
Route::post('/admin/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/admin/logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...

Route::get('/admin/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/admin/register', 'Auth\RegisterController@register');


// Password Reset Routes...
Route::get('/admin/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/admin/ajax/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('admin/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('/admin/ajax/reset', 'Auth\ResetPasswordController@reset')->name('password.ajaxrequest');

Route::get('/admin/confirmpass', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('/admin/confirmpass', 'Auth\ConfirmPasswordController@confirm')->name('password.ajaxconfirm');


Route::post('/admin/ajax/simpleupdatefield','Admin\AjaxController@simpleUpdateField')
->middleware(['auth','verified','reg_step_check'])->middleware(['dynamictypecheck:admin,editor,assistant']);

Route::post('/admin/ajax/updateownfield','Admin\AjaxController@updateOwnField')
->middleware(['auth','verified','reg_step_check']);
    
Route::post('/admin/ajax/getsingleclaimstate','Admin\AjaxController@getSingleClaimState')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');
    
Route::post('/admin/ajax/getmultipleclaimstate','Admin\AjaxController@getMultipleClaimState')
->middleware(['auth','verified','reg_step_check'])->middleware('is_admin_or_editor');

//admin


Route::get('/clear-cache-now', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});


