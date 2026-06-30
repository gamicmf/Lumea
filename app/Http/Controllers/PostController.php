<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function reportPost($request, $id){
        $request->validate([
            'reportable_id' => 'required|integer',
            'reportable_type' => 'required|string',
            'description' => 'required|string|max:255',
        ]);
        $report = new Report();
        $report->id_user = Auth::id();
        $report->reportable_id = $id;
        $report->reportable_type = 'user';
        $report->description = $request->input('description');
        $report->created_at = now();
        $report->updated_at = now();

        $report->save();

        return redirect()->back()->with('success', 'Post reported successfully!');
    }
}
