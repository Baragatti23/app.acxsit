<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CalculeController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\BordereauController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\LicenceController;
use App\Http\Controllers\LivrerController;
use App\Http\Controllers\StadeController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProfilController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->group(function(){
    Route::post('/signup','register');
    Route::post('/signin','login');
    Route::post('/loged','loged');
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');
});
Route::controller(LicenceController::class)->group(function(){
    Route::get('/licences','get');
    Route::get('/licence/{id}','get');
    Route::post('/licences/','post');
    Route::delete('/licences/','del');
    Route::put("/licences/",'put');
});
Route::controller(LivrerController::class)->group(function(){
    Route::get('/livrers','get');
    Route::get('/livrer/{id}','get');
    Route::delete('/livrers/{id}','del');
    Route::post("/livrers/",'post');
    Route::put("/livrers/",'put');
});

Route::controller(StadeController::class)->group(function(){
    Route::get('/stades','get');
    Route::get('/stade/{id}','get');
});
Route::controller(CategorieController::class)->group(function(){
    Route::get('/categories','get');
    Route::get('/categories/to_product','to_create_products');
    Route::get('/categorie/{id}','get');
});
Route::controller(FournisseurController::class)->group(function(){
    Route::get('/fournisseurs','get');
    Route::get('/fournisseurs/to_product','to_create_products');
    Route::get('/fournisseur/{id}','get');
});
Route::controller(EstadoController::class)->group(function(){
    Route::get('/estados','get');
    Route::get('/estado/{id}','get');
});

Route::controller(ProfilController::class)->group(function(){
    Route::get('/profils','get');
    Route::get('/profils/to_user','to_create_user');
});




Route::controller(DashboardController::class)->group(function(){
    Route::get('/dashboard','get');
});
Route::controller(CalculeController::class)->group(function(){
    Route::get('/calcules','get');
    Route::get('/calcule/{id_proforma}/{id_product}','get');
    Route::post('/calcules/','post');
    Route::delete('/calcules/','del');
    Route::put('/calcules/{id_proforma}/{id_product}','put');
});
Route::controller(EquipementController::class)->group(function(){
    Route::get('/equipements','get');
    Route::get('/equipements/to_calcule/{id}','to_create_calcules');
    Route::get('/equipement/{id}','get');
    Route::delete('/equipements/{id}','del');
    Route::post("/equipements/",'post');
    Route::put("/equipements/{id}",'put');
});
Route::controller(FormationController::class)->group(function(){
    Route::get('/formations','get');
    Route::get('/formations/to_calcule','to_create_calcules');
    Route::get('/formation/{id}','get');
    Route::delete('/formations/{id}','del');
    Route::post("/formations/",'post');
    Route::put("/formations/{id}",'put');
});
Route::controller(ClientController::class)->group(function(){
    Route::get('/clients','get');
    Route::get('/clients/to_proforma','to_create_proformas');
    Route::get('/clients/{id}','get');
    Route::delete('/clients/{id}','del');
    Route::post("/clients/",'post');
    Route::put("/clients/{id}",'put');
});
Route::controller(UtilisateurController::class)->group(function(){
    Route::get('/utilisateurs','get');
    Route::get('/utilisateur/{id}','get');
    Route::delete('/utilisateurs/{id}','del');
    Route::put("/utilisateurs/{id}",'put');
});
Route::controller(ProformaController::class)->group(function(){
    Route::get('/proformas','get');
    Route::get('/proformas/status/{id}','get_status');
    Route::get('/proformas/to_bordereau_livraison','to_create_bordereaus');
    Route::get('/proformas/pdf/get/{id}','getPDF');
    Route::get('/proformas/pdf/download/{id}','downloadPDF');
    Route::get('/proforma/{id}','get');
    Route::get('/proformas/{id}','get');
    Route::delete('/proformas/{id}','del');
    Route::post('/proformas/','post');
    Route::put('/proformas/{id}','put');
});
Route::controller(BordereauController::class)->group(function(){
    Route::get('/bordereaus','get');
    Route::get('/bordereaus/status/{id}','get_status');
    Route::get('/bordereaus/pdf/{id}','getPDF');
    Route::get('/bordereaus/download/{id}','downloadPDF');
    Route::get('/bordereau/{id}','get');
    Route::delete('/bordereaus/{id}','del');
    Route::post('/bordereaus/','post');
    Route::put('/bordereaus/{id}','put');
});