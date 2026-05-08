<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    /**
     * Hiển thị danh sách các cuộc trò chuyện.
     */
    public function index()
    {
        // Lấy danh sách các cuộc trò chuyện
        $sessions = ChatSession::with('user')
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.chat.index', compact('sessions'));
    }

    /**
     * Hiển thị thông tin chi tiết của một cuộc trò chuyện.
     */
    public function show($id)
    {
        $session = ChatSession::with(['messages', 'user'])->findOrFail($id);

        return view('admin.chat.show', compact('session'));
    }
}
