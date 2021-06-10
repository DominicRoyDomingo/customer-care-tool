<?php

use Illuminate\Http\Request;

use App\Http\Controllers\LocationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



/*
|--------------------------------------------------------------------------
| Customer Care Tool API Routes
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => '\CRM\API', 'middleware' => 'auth:api'], function () {
    foreach (scandir($path = base_path('CRM/API')) as $dirName) {
        if (file_exists($filepath = $path . '/' . $dirName . '/Route.php')) {
            include $filepath;
        }
    }
});
