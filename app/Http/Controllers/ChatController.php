<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $contacts = $this->getAllowedContacts($user);
        
        // Load awal (HTML render pertama kali)
        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages' => function($query) {
                $query->latest()->first();
            }])
            ->latest('updated_at')
            ->get();

        return view('chat.index', compact('contacts', 'conversations'));
    }

    // ... (Method getAllowedContacts dan checkOrCreateRoom BIARKAN SAMA SEPERTI SEBELUMNYA) ...
    private function getAllowedContacts($currentUser) {
        // ... (Paste logika getAllowedContacts kamu yang lama di sini) ...
        // Agar tidak kepanjangan, saya skip tulis ulang karena logika kamu sudah benar.
        $query = User::query();
        $query->where('id', '!=', $currentUser->id);

        if ($currentUser->role === 'super_admin') {
            $query->where('role', 'owner');
        } elseif ($currentUser->role === 'owner') {
            $shopId = $currentUser->shop_id;
            $query->where(function($q) use ($shopId) {
                $q->where('role', 'super_admin')
                  ->orWhere(function($subQ) use ($shopId) {
                      $subQ->whereIn('role', ['cashier','inventory'])->where('shop_id', $shopId);
                  });
            });
        } elseif ($currentUser->role === 'cashier' || $currentUser->role === 'inventory') {
            $shopId = $currentUser->shop_id;
            $query->where('shop_id', $shopId)->whereIn('role', ['owner', 'cashier', 'inventory']);
        }
        return $query->get();
    }

    public function checkOrCreateRoom($receiverId) {
        // ... (Paste logika checkOrCreateRoom kamu yang lama di sini) ...
        $sender = Auth::user();
        $receiver = User::findOrFail($receiverId);
        $conversation = Conversation::where(function($q) use ($sender, $receiver) {
            $q->where('user_one_id', $sender->id)->where('user_two_id', $receiver->id);
        })->orWhere(function($q) use ($sender, $receiver) {
            $q->where('user_one_id', $receiver->id)->where('user_two_id', $sender->id);
        })->first();

        if (!$conversation) {
            $shopId = $sender->shop_id ?? $receiver->shop_id;
            $conversation = Conversation::create([
                'shop_id' => $shopId, 
                'user_one_id' => $sender->id,
                'user_two_id' => $receiver->id,
            ]);
        }
        return redirect()->route('chat.show', $conversation->id);
    }
    // ... (Akhir bagian yang sama) ...

    public function show(Conversation $conversation)
    {
        if ($conversation->user_one_id != Auth::id() && $conversation->user_two_id != Auth::id()) {
            abort(403);
        }

        $user = Auth::user();
        $contacts = $this->getAllowedContacts($user);
        
        // Ambil conversation untuk sidebar
        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages' => function($q) {
                $q->latest()->first();
            }])
            ->latest('updated_at')
            ->get();

        // Ambil pesan
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai READ saat membuka halaman (Initial Load)
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('chat.index', compact('contacts', 'conversations', 'conversation', 'messages'));
    }

    // Di method store()
public function store(Request $request, Conversation $conversation)
{
    // Validasi: body boleh null KALO ada attachment
    $request->validate([
        'body' => 'nullable',
        'attachment' => 'nullable|file|mimes:audio,webm,mp3,wav,ogg'
    ]);

    $data = [
        'sender_id' => Auth::id(),
        'body' => $request->body ?? 'Voice Note', // Fallback text
        'type' => 'text',
        'attachment' => null
    ];

    // Cek jika ada file audio yang dikirim
    if ($request->hasFile('audio_blob')) {
        // Simpan ke folder public/voice_notes
        $path = $request->file('audio_blob')->store('voice_notes', 'public');
        
        $data['type'] = 'audio';
        $data['attachment'] = $path;
        $data['body'] = '🎤 Pesan Suara'; // Text pengganti untuk preview di sidebar
    }

    $message = $conversation->messages()->create($data);
    $conversation->touch();

    if ($request->expectsJson()) {
        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'type' => $message->type,          // <--- Tambahan
            'attachment' => $message->attachment, // <--- Tambahan
            'sender_id' => $message->sender_id,
            'created_at' => $message->created_at->toDateTimeString(),
            'formatted_time' => $message->created_at->format('H:i')
        ]);
    }

    return redirect()->route('chat.show', $conversation->id);
}

    // --- API UNTUK POLLING PESAN (LOOP A) ---
    public function fetchNewMessages(Request $request, Conversation $conversation)
    {
        if ($conversation->user_one_id != Auth::id() && $conversation->user_two_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastMessageId = $request->query('last_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // UPDATE LOGIC: Karena user sedang aktif mem-poll (membuka chat ini),
        // maka pesan yang baru diambil dianggap SUDAH DIBACA.
        if ($newMessages->count() > 0) {
            $idsToMark = $newMessages->where('sender_id', '!=', Auth::id())->pluck('id');
            if ($idsToMark->isNotEmpty()) {
                Message::whereIn('id', $idsToMark)->update(['is_read' => true]);
            }
        }

        return response()->json($newMessages);
    }

    // --- API UNTUK POLLING SIDEBAR (LOOP B) ---
    public function getConversations()
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'messages' => function($q) {
                $q->latest()->first();
            }])
            ->latest('updated_at') // Sort berdasarkan update terakhir (PENTING UNTUK NAIK KE ATAS)
            ->get()
            ->map(function($chat) use ($user) {
                $partner = ($chat->user_one_id == $user->id) ? $chat->userTwo : $chat->userOne;
                
                // Hitung pesan belum dibaca
                $unreadCount = $chat->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'id' => $chat->id,
                    'partner_name' => $partner->name,
                    'partner_initial' => substr($partner->name, 0, 1),
                    'last_message' => $chat->messages->isNotEmpty() ? $chat->messages->first()->body : 'Mulai percakapan...',
                    'last_message_time' => $chat->messages->isNotEmpty() ? $chat->messages->first()->created_at->format('H:i') : '',
                    'unread_count' => $unreadCount,
                    'updated_at_ts' => $chat->updated_at->timestamp // Untuk debugging sorting jika perlu
                ];
            });

        return response()->json($conversations);
    }

    // API Detail untuk Load AJAX
    public function detail(Conversation $conversation)
    {
         if ($conversation->user_one_id != Auth::id() && $conversation->user_two_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = Auth::user();
        $partner = ($conversation->user_one_id == $user->id) ? $conversation->userTwo : $conversation->userOne;

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai read
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'id' => $conversation->id,
            'partner' => [
                'id' => $partner->id,
                'name' => $partner->name,
            ],
            'messages' => $messages,
        ]);
    }

    public function destroy(Conversation $conversation)
    {
        if ($conversation->user_one_id != Auth::id() && $conversation->user_two_id != Auth::id()) {
            abort(403);
        }
        $conversation->messages()->delete();
        $conversation->delete();
        return redirect()->route('chat.index')->with('success', 'Percakapan dihapus');
    }
}