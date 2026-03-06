<?php
use App\Contracts\Route;

/**
 * Auth Routes
 */
Route::get('/register', 'AuthController@create');
Route::post('/register', 'AuthController@store');
Route::get('/verify', 'AuthController@verifyEmail');
Route::get('/login', 'AuthController@login');
Route::post('/login', 'AuthController@authenticate');
Route::get('/reset', 'AuthController@showResetForm');
Route::post('/reset', 'AuthController@sendResetLink');
Route::get('/reset-password', 'AuthController@resetPassword');
Route::post('/reset-password', 'AuthController@resetPassword');
Route::post('/logout', 'AuthController@logout');

/**
 * User Routes
 */
Route::get('/', 'UserController@index');
Route::get('/dashboard', 'UserController@dashboard', 'isUser');
Route::get('/profile/edit', 'UserController@edit', 'isUser');
Route::post('/profile/update','UserController@update', 'isUser');
Route::get('/change-password', 'UserController@showChangePasswordForm', 'isUser');
Route::post('/change-password', 'UserController@changePassword', 'isUser');

/**
 * Admin Routes
 */
Route::get('/admin', 'AdminController@index', 'isAdmin');
Route::get('/admin/dashboard', 'AdminController@dashboard', 'isAdmin');
Route::get('/admin/users', 'AdminController@showUsers','isAdmin');
Route::get('/admin/admin-users', 'AdminController@showAdmin', 'isAdmin');
Route::get('/admin/user/edit', 'AdminController@edit', 'isAdmin');
Route::post('/admin/user/update', 'AdminController@update', 'isAdmin');
Route::post('/admin/user/delete', 'AdminController@destroy', 'isAdmin');
Route::get('/admin/edit-profile', 'AdminController@editProfile', 'isAdmin');
Route::post('/admin/edit-profile', 'AdminController@updateProfile', 'isAdmin');
Route::post('/admin/reset-password', 'AdminController@changePassword', 'isAdmin');
Route::post('/admin/user/approved-status', 'AdminController@updateApprovedStatus', 'isAdmin');
Route::post('/admin/user/active-status', 'AdminController@updateActiveStatus', 'isAdmin');
Route::get('/admin/settings', 'AdminController@settings', 'isAdmin');
Route::post('/admin/settings/update', 'AdminController@updateSettings', 'isAdmin');