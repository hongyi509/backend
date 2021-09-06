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

Route::fallback(function () {
    //En dev on redirige vers le serveur de webpack
    //return redirect('http://localhost:3000');
    die('This should not show up');

    //En prod le serveur viendra jamais ici, il ne sert sur index.php *QUE* /api , le reste passe par index.html
});
