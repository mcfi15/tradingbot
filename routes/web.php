<?php

use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('trading-bots', [HomeController::class, 'tradingBots'])->name('trading-bots');
Route::get('copy-trading', [HomeController::class, 'copyTrading'])->name('copy-trading');

Route::get('about', [HomeController::class, 'aboutUs'])->name('about');
Route::get('license', [HomeController::class, 'license'])->name('license');

Route::get('lang/{locale}', function ($locale) {
    $supported_locales = config('languages');
    if (!array_key_exists($locale, $supported_locales)) {
        return redirect()->back();
    }
    session(['locale' => $locale]);
    // if the user is logged, update the 'lang"
    if (config('app.env') === 'sandbox') {
        return redirect()->back();
    }
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->user()->lang = $locale;
        Auth::guard('admin')->user()->save();
    } elseif (Auth::check()) {
        Auth::user()->lang = $locale;
        Auth::user()->save();
    }
    return redirect()->back();
})->name('lang.switch');



Route::get('contact', [HomeController::class, 'contact'])->name('contact');
Route::post('contact', [HomeController::class, 'contactSend'])->name('contact.send');

Route::get('privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('terms-and-conditions', [HomeController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('risk-disclosure', [HomeController::class, 'riskDisclosure'])->name('risk-disclosure');


// Auth Routes
