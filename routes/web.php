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

Route::get("/login",  [\App\Http\Controllers\LoginController::class, 'Show'])->name("login");
Route::post("/login",  [\App\Http\Controllers\LoginController::class, 'Login']);

Route::middleware(['auth'])->group(function () {

    Route::get("/",  [\App\Http\Controllers\DashboardController::class, 'main']);
    Route::get("/search",  [\App\Http\Controllers\SearchController::class, 'show']);
    Route::post("/doSearch",  [\App\Http\Controllers\SearchController::class, 'search']);
    Route::get("/doSearch",  [\App\Http\Controllers\SearchController::class, 'search']);
    Route::get("/directory/{directory}",  [\App\Http\Controllers\ShowLibraryController::class, 'redirectDirectory']);
    Route::get("/file/{comic}",  [\App\Http\Controllers\ShowLibraryController::class, 'redirectSingleFile']);
    Route::get("/file/{comic}/download",  [\App\Http\Controllers\ShowLibraryController::class, 'DownloadSingleComic']);

    Route::get("/logout",  [\App\Http\Controllers\LoginController::class, 'Logout']);
    Route::get("/changePassword",  [\App\Http\Controllers\LoginController::class, 'changePassword']);
    Route::post("/changePassword",  [\App\Http\Controllers\LoginController::class, 'doChangePassword']);
    Route::get("/settings",  [\App\Http\Controllers\UserSettingsController::class, 'show']);
    Route::put("/settings",  [\App\Http\Controllers\UserSettingsController::class, 'store']);


    Route::get("/library/{id}",  [\App\Http\Controllers\ShowLibraryController::class, 'Show']);
    Route::get("/library/{id}/{dir}",  [\App\Http\Controllers\ShowLibraryController::class, 'ShowDir']);
    Route::get("/library/{id}/{dir}/{comic}",  [\App\Http\Controllers\ShowLibraryController::class, 'ShowComic']);
    Route::get("/library/{id}/{dir}/{comic}/download",  [\App\Http\Controllers\ShowLibraryController::class, 'DownloadComic']);



});


Route::middleware(['auth.basic'])->group(function () {

    //OPDS
    Route::get("/opds",  [\App\Http\Controllers\OPDSController::class, 'ShowMainLibraries']);
    Route::get("/opds-{id}",  [\App\Http\Controllers\OPDSController::class, 'ShowMain']);
    Route::get("/opds-{id}/opds",  [\App\Http\Controllers\OPDSController::class, 'ShowMain']);
    Route::get("/opds-{id}/latest",  [\App\Http\Controllers\OPDSController::class, 'ShowLatest']);
    Route::get("/opds-{id}/opds/latest",  [\App\Http\Controllers\OPDSController::class, 'ShowLatest']);
    Route::get("/opds-{id}/all",  [\App\Http\Controllers\OPDSController::class, 'ShowAll']);
    Route::get("/opds-{id}/opds/all",  [\App\Http\Controllers\OPDSController::class, 'ShowAll']);
    Route::get("/opds-download/{comic}/download",  [\App\Http\Controllers\ShowLibraryController::class, 'DownloadSingleComic']);
});


Route::middleware(['auth', 'is.admin'])->group(function () {

    //Admins only
    Route::resources([
        'manage/libraries' => \App\Http\Controllers\Manage\LibraryController::class,
    ]);
    Route::resources([
        'manage/importopds' => \App\Http\Controllers\Manage\ImportOPDSController::class,
    ]);
    Route::resources([
        'manage/users' => \App\Http\Controllers\Manage\UsersController::class,
    ]);

   /* Route::resources([
        'manage/mounts' => \App\Http\Controllers\Manage\MountsController::class,
    ]);*/

    Route::get("/manage/settings",  [\App\Http\Controllers\Manage\SettingsController::class, 'Show']);
    Route::put("/manage/settings",  [\App\Http\Controllers\Manage\SettingsController::class, 'store']);


    Route::get('/api/manage/libraries/folders/browse/{base64path}', function (Request $request, $base64path) {

        $path = base64_decode($base64path);

        $dirs = glob(str_replace("..", "", $path) . '/*', GLOB_ONLYDIR);

        return json_encode($dirs);
    });

    Route::get('/api/manage/libraries/folders/exists/{base64path}', function (Request $request, $base64path) {

        $path = base64_decode($base64path);

        return \App\Models\LibraryFolder::where("path", "=", $path)->exists();
    });

});
