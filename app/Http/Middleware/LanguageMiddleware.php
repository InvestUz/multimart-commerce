<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if language is set in session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        
        // Check if language is set in request (for language switch)
        if ($request->has('lang')) {
            $locale = $request->get('lang');
            
            // Validate locale
            if (in_array($locale, ['en', 'ru', 'uz'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        }
        
        return $next($request);
    }
}