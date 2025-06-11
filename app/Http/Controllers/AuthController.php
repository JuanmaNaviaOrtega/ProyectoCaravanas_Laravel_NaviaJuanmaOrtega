<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

   public function login(Request $request)
{
    $credentials = $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Redirige siempre a la página principal
        return redirect()->route('home');
    }

    return back()->withErrors([
        'email' => 'Credenciales incorrectas.',
    ]);
}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

  public function register(Request $request)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    // Por defecto, los nuevos usuarios no son administradores
    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => bcrypt($validated['password']),
        'is_admin' => false,
    ]);
  $user->sendEmailVerificationNotification();
    Auth::login($user);

    // Redirige siempre a la página principal
     return redirect('/email/verify');
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
