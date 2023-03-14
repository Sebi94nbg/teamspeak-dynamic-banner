<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\BannerConfigurationController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BannerTemplateController;
use App\Http\Controllers\BannerVariableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Setup\Installer\RequirementsController;
use App\Http\Controllers\Setup\Installer\UserController;
use App\Http\Controllers\TemplateController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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

Auth::routes(['register' => false]);

Route::get('/', function () {
    if (User::all()->count() == 0) {
        return Redirect::route('setup.installer.requirements');
    }

    return Redirect::route('dashboard');
});

Route::get('/setup/installer/requirements', [RequirementsController::class, 'show_view'])->name('setup.installer.requirements');

Route::get('/setup/installer/user', [UserController::class, 'show_view'])->name('setup.installer.user');
Route::post('/setup/installer/user/add', [UserController::class, 'create'])->name('setup.installer.user.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/instances', [InstanceController::class, 'overview'])->name('instances');
    Route::get('/instance/add', [InstanceController::class, 'add'])->name('instance.add');
    Route::post('/instance/save', [InstanceController::class, 'save'])->name('instance.save');
    Route::get('/instance/{instance_id}/edit', [InstanceController::class, 'edit'])->name('instance.edit');
    Route::patch('/instance/{instance_id}/update', [InstanceController::class, 'update'])->name('instance.update');
    Route::delete('/instance/{instance_id}/delete', [InstanceController::class, 'delete'])->name('instance.delete');
    Route::post('/instance/{instance_id}/start', [InstanceController::class, 'start'])->name('instance.start');
    Route::post('/instance/{instance_id}/stop', [InstanceController::class, 'stop'])->name('instance.stop');
    Route::post('/instance/{instance_id}/restart', [InstanceController::class, 'restart'])->name('instance.restart');

    Route::get('/templates', [TemplateController::class, 'overview'])->name('templates');
    Route::get('/template/add', [TemplateController::class, 'add'])->name('template.add');
    Route::post('/template/save', [TemplateController::class, 'save'])->name('template.save');
    Route::get('/template/{template_id}/edit', [TemplateController::class, 'edit'])->name('template.edit');
    Route::patch('/template/{template_id}/update', [TemplateController::class, 'update'])->name('template.update');
    Route::delete('/template/{template_id}/delete', [TemplateController::class, 'delete'])->name('template.delete');

    Route::get('/banners', [BannerController::class, 'overview'])->name('banners');
    Route::get('/banner/add', [BannerController::class, 'add'])->name('banner.add');
    Route::post('/banner/save', [BannerController::class, 'save'])->name('banner.save');
    Route::get('/banner/{banner_id}/edit', [BannerController::class, 'edit'])->name('banner.edit');
    Route::patch('/banner/{banner_id}/update', [BannerController::class, 'update'])->name('banner.update');
    Route::delete('/banner/{banner_id}/delete', [BannerController::class, 'delete'])->name('banner.delete');

    Route::get('/banner/{banner_id}/variables', [BannerVariableController::class, 'overview'])->name('banner.variables');

    Route::get('/banner/{banner_id}/templates', [BannerTemplateController::class, 'edit'])->name('banner.templates');
    Route::post('/banner/template/add', [BannerTemplateController::class, 'add'])->name('banner.template.add');
    Route::delete('/banner/template/remove', [BannerTemplateController::class, 'remove'])->name('banner.template.remove');
    Route::patch('/banner/template/disable', [BannerTemplateController::class, 'disable'])->name('banner.template.disable');
    Route::patch('/banner/template/enable', [BannerTemplateController::class, 'enable'])->name('banner.template.enable');

    Route::get('/banner/{banner_id}/configuration/{template_id}', [BannerConfigurationController::class, 'edit'])->name('banner.template.configuration.edit');
    Route::patch('/banner/{banner_id}/configuration/{template_id}', [BannerConfigurationController::class, 'upsert'])->name('banner.template.configuration.upsert');
    Route::get('/banner/configuration/{banner_configuration_id}/delete', [BannerConfigurationController::class, 'delete'])->name('banner.template.configuration.delete');

    Route::get('/administration/users', [AdministrationController::class, 'users'])->name('administration.users');
    Route::get('/administration/user/add', [AdministrationController::class, 'add_user'])->name('administration.user.add');
    Route::post('/administration/user/create', [AdministrationController::class, 'create_user'])->name('administration.user.create');
    Route::get('/administration/user/{user_id}/edit', [AdministrationController::class, 'edit_user'])->name('administration.user.edit');
    Route::patch('/administration/user/{user_id}/update', [AdministrationController::class, 'update_user'])->name('administration.user.update');
    Route::delete('/administration/user/{user_id}/delete', [AdministrationController::class, 'delete_user'])->name('administration.user.delete');

    Route::get('/administration/phpinfo', [AdministrationController::class, 'php_info'])->name('administration.phpinfo');
    Route::get('/administration/systemstatus', [AdministrationController::class, 'system_status'])->name('administration.systemstatus');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'change_password'])->name('profile.update.password');
});
