<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NotificationPusher;


class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $users = User::where('username', 'LIKE', "%{$query}%")
            ->take(3)
            ->get(['username']); // Retorna apenas campos necessários
        return response()->json($users);
    }
    public function autocomplete(Request $request)
    {
        $username = $request->get('username'); // Alterado de 'query' para 'username'
        
        $users = User::where('username', 'like', '%' . $username . '%')
                     ->take(10) // Limita os resultados retornados
                     ->get(['id', 'username']); // Retorna apenas os campos necessários
        
        return response()->json($users);
    }
    
    

    public function searchUser(Request $request)
    {
        // Recebe o username da query string
        $username = $request->input('username');
    
        // Busca o usuário pelo username
        $user = User::where('username', $username)->first();
    
        // Se o usuário for encontrado, redireciona para o perfil do usuário
        if ($user) {
            return redirect()->route('profile.showByUsername', ['username' => $user->username]);
        }
    
        // Se não encontrar o usuário, redireciona de volta com uma mensagem de erro
        return redirect()->route('publications.index')->with('error', 'User not found.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if (Auth::check() && (Auth::user()->id == $user->id || Auth::user()->isAdmin())) {
            Auth::logout(); // Deslogar o usuário antes de deletar a conta
            $user->delete();
            return redirect()->route('login')->with('status', 'Account deleted successfully.');
        }

        return redirect()->back()->with('error', 'You do not have permission to delete this account.');
    }
}
