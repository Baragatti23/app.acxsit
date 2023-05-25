<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CalculeController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\BordereauController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\LivrerController;
use App\Http\Controllers\StadeController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\FormationController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->group(function(){
    Route::post('/signup','register');
    Route::post('/0auth','login');
    Route::post('/loged','loged');
    Route::post('/logout',[AuthController::class,'logout']);
});
Route::controller(UtilisateurController::class)->group(function(){
    // echo($_SERVER["REQUEST_URI"])."\n";
    // echo $_SERVER["REQUEST_METHOD"]."\n";
    Route::get('/utilisateurs','get');
    Route::get('/utilisateur/{id}','get');
    Route::delete('/utilisateurs/{id}','del');
    Route::post("/utilisateurs/",'post');
});
Route::controller(LicenceController::class)->group(function(){
    Route::get('/licences','get');
    Route::get('/licence/{id}','get');
    Route::post('/licences/','post');
});
Route::controller(FormationController::class)->group(function(){
    Route::get('/formations','get');
    Route::get('/formation/{id}','get');
    Route::post('/formations/','post');
});
Route::controller(LivrerController::class)->group(function(){
    Route::get('/livrers','get');
    Route::get('/livrer/{id}','get');
    Route::post('/livrers/','post');
});
Route::controller(EquipementController::class)->group(function(){
    Route::get('/equipements','get');
    Route::get('/equipement/{id}','get');
});
Route::controller(StadeController::class)->group(function(){
    Route::get('/stades','get');
    Route::get('/stade/{id}','get');
});
Route::controller(EstadoController::class)->group(function(){
    Route::get('/estados','get');
    Route::get('/estado/{id}','get');
});
Route::controller(ClientController::class)->group(function(){
    Route::get('/clients','get');
    Route::get('/client/{id}','get');
    Route::post("/clients/",'post');
});
Route::controller(CalculeController::class)->group(function(){
    Route::get('/calcules','get');
    Route::get('/calcule/{id}','get');
    Route::post('/calcules/','post');
    Route::put('/calcules/{id_proforma}/{id_equipement}','put');
});
Route::controller(ProformaController::class)->group(function(){
    Route::get('/proformas','get');
    Route::get('/proformas/pdf/get/{id}','getPDF');
    Route::get('/proformas/pdf/download/{id}','downloadPDF');
    Route::get('/proforma/{id}','get');
    Route::delete('/proformas/{id}','del');
    Route::post('/proformas/','post');
    Route::put('/proformas/status/{id}/{status}','changeStatus');
});
Route::controller(BordereauController::class)->group(function(){
    Route::get('/bordereaus','get');
    Route::get('/bordereaus/pdf/get/{id}','getPDF');
    Route::get('/bordereaus/pdf/download/{id}','downloadPDF');
    Route::get('/bordereau/{id}','get');
    Route::delete('/bordereaus/{id}','del');
    Route::post('/bordereaus/','post');
    Route::put('/bordereaus/status/{id}/{status}','changeStatus');

});
// Route::controller(ApiController::class)->group(function(){
//     Route::get('/{table}/{id?}','get');
//     Route::post('/{table}','post');
//     Route::put('/{table}/{id}','put');
//     Route::delete('/{table}/{id}','del');
// });