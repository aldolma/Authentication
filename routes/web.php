<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
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

Route::get('/auth/github/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/auth/github/callback', function () {
    try {
        $socialiteUser = Socialite::driver('github')->user();
    } catch (\Exception $e) {
        return redirect('/login');
    }

    $user = User::where([
        'provider'=>'github',
        'provider_id'=>$socialiteUser->getId(),
    ])->first();

    if(!$user){
        $validator = Validator::make(
            ['email' => $socialiteUser->getEmail()],
            ['email'=> ['unique:users,email']],
            ['email.unique'=>'Couldnt log in. use different login method!']
        );

        if($validator->fails()){
            return redirect('/login')->withErrors($validator);
        }

        $user = User::create([
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'provider' => 'github',
            'provider_id' => $socialiteUser->getId(),
            'email_verified_at' => now()
        ]);
    }

    Auth::login($user);
    return redirect('/home');

    dd($user->getName(),$user->getEmail(),$user->getId());
    // $user->token
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
