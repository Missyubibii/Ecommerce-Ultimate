<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    /**
     * Display a listing of the chat sessions.
     */
    public function index()
    {
        // Get sessions ordered by latest update (most recent conversation)
        $sessions = ChatSession::with('user')
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->paginate(20);
            
        return view('admin.chat.index', compact('sessions'));
    }

    /**
     * Display the specified chat session.
     */
    public function show($id)
    {
        $session = ChatSession::with(['messages', 'user'])->findOrFail($id);
        
        return view('admin.chat.show', compact('session'));
    }
}
