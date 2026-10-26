<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Playground only: log in as the first user without the login form (local env).
if (app()->environment('local')) {
    // Optional ?as=email to pick a user, e.g. /dev-login?as=super@example.com
    Route::get('/dev-login', function () {
        $query = \App\Models\User::query();

        if ($email = request('as')) {
            $query->where('email', $email);
        }

        auth()->login($query->firstOrFail());

        return redirect('/nova/nova-settings');
    });
}
