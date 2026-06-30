<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\Publication;
use Illuminate\Support\Facades\Auth;
use App\Events\NotificationPusher;


class VoteController extends Controller
{
    public function create($id)
    {
        $publication = Publication::findOrFail($id);

        // Verifique se o usuário já votou nessa publicação
        $userVote = Vote::where('id_publication', $id)
                        ->where('id_post', Auth::id())
                        ->first();

        if ($userVote) {
            return redirect()->route('publications.show', $id)->with('error', 'You have already rated this publication.');
        }

        return view('publications.rate', compact('publication'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'aesthetic' => 'required|integer|min:0|max:100',
            'technique' => 'required|integer|min:0|max:100',
            'creativity' => 'required|integer|min:0|max:100',
        ]);
    
        $publication = \App\Models\Publication::findOrFail($id);
    
        // Verificar se o usuário já votou
        $existingVote = \App\Models\Vote::where('id_publication', $id)
                            ->where('id_post', Auth::id())
                            ->first();
    
        if ($existingVote) {
            return redirect()->route('publications.show', $id)->with('error', 'You have already rated this publication.');
        }
    
        // Calcular a média do novo voto
        $rate = ($request->aesthetic + $request->technique + $request->creativity) / 3;
    
        // Criar o novo voto
        \App\Models\Vote::create([
            'id_post' => Auth::id(),
            'id_publication' => $id,
            'aesthetic' => $request->aesthetic,
            'technique' => $request->technique,
            'creativity' => $request->creativity,
            'rate' => $rate,
            'created_date' => now(),
        ]);
    
        // Atualizar o ranking da publicação com a média dos votos
        $averageRate = \App\Models\Vote::where('id_publication', $id)->avg('rate');
        $publication->ranking = $averageRate;
        $publication->save();
    
        return redirect()->route('publications.show', $id)->with('success', 'Thank you for your rating!');
    }
    
}
