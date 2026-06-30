<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;


class LoginController extends Controller
{
    protected $redirectTo = '/publications';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where('email', $request->email)->orWhere('username', $request->email)->first();

        if ($user) {
            // Verificar se a senha está em texto simples
            if (!Hash::check($request->password, $user->password)) {
                // Se a senha não estiver em hash, verificar se corresponde ao texto simples
                if ($user->password === $request->password) {
                    // Atualizar a senha para hash
                    $user->password = Hash::make($request->password);
                    $user->save();
                } else {
                    // Senha incorreta
                    throw ValidationException::withMessages([
                        'email' => [trans('auth.failed')],
                    ]);
                }
            }
            if ($user->blocked) {
                return redirect()->back()->withInput($request->only('email', 'remember'))
                    ->withErrors(['email' => 'This account is blocked.']);
            }    
            // Tentar autenticar o usuário
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password], $request->filled('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended($this->redirectPath());
            }
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    protected function redirectPath()
    {
        return $this->redirectTo;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            Auth::login($user);
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'username' => $googleUser->getNickname() ?? $googleUser->getEmail(),
                'password' => Hash::make(uniqid()),
                'profile_picture' => $googleUser->getAvatar(),
            ]);
            Auth::login($user);
        }

        return redirect($this->redirectPath());
    }
}


/*
class LoginController extends Controller
{
    protected $redirectTo = '/publications';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $fielType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        
        if (Auth::attempt([$fielType=>$request->email, 'password'=>$request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }
        

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    

    protected function redirectPath()
    {
        return $this->redirectTo;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}*/
