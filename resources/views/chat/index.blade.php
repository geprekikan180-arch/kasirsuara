@extends('layouts.chat') 

@section('content')
@php
    $dashboardRoute = match(auth()->user()->role) {
        'super_admin' => 'superadmin.dashboard',
        'owner' => 'owner.dashboard',
        'cashier' => 'cashier.dashboard',
        'inventory' => 'inventory.dashboard',
        default => 'chat.index'
    };
@endphp

<div class="flex h-screen antialiased text-gray-800 bg-white overflow-hidden rounded-lg shadow-lg">
    
    {{-- SIDEBAR --}}
    <div class="flex flex-col w-80 border-r border-gray-200 bg-gray-50">
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <a href="{{ route($dashboardRoute) }}" class="p-2 hover:bg-gray-100 rounded-full transition" title="Kembali ke Dashboard">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h2 class="text-xl font-bold text-gray-800">Chat</h2>
                </div>
                <button onclick="document.getElementById('contactModal').classList.remove('hidden')" 
                        class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>
            {{-- SEARCH BAR --}}
            <input type="text" id="convSearch" placeholder="Cari percakapan..." class="w-full mt-3 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-2" id="conversationsList">
            {{-- List akan di-render ulang oleh JavaScript --}}
            {{-- Initial Render dari Server (untuk SEO/First Paint) --}}
            @foreach($conversations as $conv)
                @php
                    $partner = ($conv->user_one_id == auth()->id()) ? $conv->userTwo : $conv->userOne;
                    $lastMsg = $conv->messages->isEmpty() ? null : $conv->messages->first();
                    // Hitung unread (server-side logic)
                    $unread = $conv->messages()->where('sender_id', '!=', auth()->id())->where('is_read', false)->count();
                    // Jika ini chat yg sedang dibuka, paksa unread jadi 0 secara visual
                    $isActive = isset($conversation) && $conversation->id == $conv->id;
                    if($isActive) $unread = 0;
                @endphp
                
                {{-- ID elemen HTML penting untuk JS selector --}}
                <div id="conv-item-{{ $conv->id }}" data-chat-id="{{ $conv->id }}" class="conv-item">
                    <a href="{{ route('chat.show', $conv->id) }}" 
                       class="flex items-center p-3 rounded-lg transition {{ $isActive ? 'bg-blue-100 border-l-4 border-blue-600' : 'hover:bg-gray-100' }}">
                        
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold relative">
                            {{ substr($partner->name, 0, 1) }}
                            {{-- Badge Merah --}}
                            <span class="unread-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold {{ $unread > 0 ? '' : 'hidden' }}">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        </div>
                        
                        <div class="ml-3 overflow-hidden w-full">
                            <div class="flex justify-between items-baseline">
                                <div class="flex items-center">
                                    <span class="font-semibold text-gray-900 truncate name-el">{{ $partner->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500 time-el">{{ $lastMsg ? $lastMsg->created_at->format('H:i') : '' }}</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate msg-preview-el">
                                {{ $lastMsg ? (auth()->id() == $lastMsg->sender_id ? 'Anda: ' : '') . $lastMsg->body : 'Mulai percakapan...' }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- MAIN CHAT AREA --}}
    <div id="chatMain" class="flex flex-col flex-auto h-full bg-white relative">
        @if(isset($conversation))
            @php
                $activePartner = ($conversation->user_one_id == auth()->id()) ? $conversation->userTwo : $conversation->userOne;
            @endphp

            {{-- HEADER --}}
            <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white shadow-sm z-10">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr($activePartner->name, 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <h3 class="font-bold text-gray-800">{{ $activePartner->name }}</h3>
                        {{-- <span class="text-xs text-green-500 flex items-center">
                            <span class="h-2 w-2 rounded-full bg-green-500 mr-1"></span> Online
                        </span> --}}
                    </div>
                </div>
                <div class="flex gap-2 items-center">
                    <form action="{{ route('chat.destroy', $conversation->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus percakapan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-full transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- MESSAGES --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messagesContainer">
                @foreach($messages as $msg)
                    @php $isMe = $msg->sender_id == auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $msg->id }}">
                        <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow {{ $isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border' }}">
                            
                            {{-- LOGIC BARU: Cek apakah pesannya tipe AUDIO atau TEKS --}}
                            @if(isset($msg->type) && $msg->type === 'audio' && $msg->attachment)
                                {{-- Jika Audio, tampilkan Player --}}
                                <audio controls class="max-w-[200px] h-10 mt-1 mb-1">
                                    {{-- Pastikan folder storage sudah di-link (php artisan storage:link) --}}
                                    <source src="{{ asset('storage/' . $msg->attachment) }}" type="audio/webm">
                                    <source src="{{ asset('storage/' . $msg->attachment) }}" type="audio/ogg">
                                    <source src="{{ asset('storage/' . $msg->attachment) }}" type="audio/mp3">
                                    Browser tidak support audio.
                                </audio>
                            @else
                                {{-- Jika Teks, tampilkan tulisan biasa --}}
                                <p class="text-sm">{{ $msg->body }}</p>
                            @endif

                            <span class="text-[10px] block text-right mt-1 {{ $isMe ? 'text-blue-200' : 'text-gray-400' }}">
                                {{ $msg->created_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- INPUT --}}
            <div class="p-4 bg-white border-t border-gray-200">
                <form id="messageForm" class="flex gap-2 items-center">
    {{-- Input Text --}}
    <input type="text" id="messageInput" name="body" placeholder="Tulis pesan..." autocomplete="off"
           class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
    
    {{-- Tombol Mic (Toggle) --}}
    <button type="button" id="recordBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full p-2 w-10 h-10 flex items-center justify-center shadow transition">
        {{-- Ikon Mic --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
        </svg>
    </button>

    {{-- Indikator Recording (Hidden by default) --}}
    <div id="recordingIndicator" class="hidden text-red-500 text-xs font-bold animate-pulse">
        Merekam...
    </div>

    {{-- Tombol Kirim Text --}}
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 w-10 h-10 flex items-center justify-center shadow">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
    </button>
</form>
            </div>

        @else
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-lg">Pilih kontak untuk mulai chat</p>
            </div>
        @endif
    </div>
</div>

{{-- MODAL KONTAK --}}
<div id="contactModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('contactModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Mulai Chat Baru</h3>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @forelse($contacts as $contact)
                        <a href="{{ route('chat.start', $contact->id) }}" class="flex items-center p-3 hover:bg-blue-50 rounded-lg transition border border-gray-100">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                {{ substr($contact->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $contact->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $contact->role }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada kontak yang tersedia.</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('contactModal').classList.add('hidden')">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UTAMA --}}
<script>
    const CURRENT_USER_ID = {{ auth()->id() }};
    const CSRF_TOKEN = "{{ csrf_token() }}";
    
    // ID Percakapan yang sedang aktif (null jika tidak ada)
    let activeConversationId = {{ isset($conversation) ? $conversation->id : 'null' }};
    
    // ID pesan terakhir untuk polling pesan baru di chat aktif
    let lastMessageId = {{ isset($messages) && !$messages->isEmpty() ? $messages->last()->id : 0 }};

    // Elemen DOM
    const msgContainer = document.getElementById('messagesContainer');
    const conversationsList = document.getElementById('conversationsList');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const searchInput = document.getElementById('convSearch');

    // --- FUNGSI HELPER ---
    function scrollToBottom() {
        if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    function escapeHtml(unsafe) {
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function truncate(text, n = 40) {
        return (text.length > n) ? text.slice(0, n-1) + '...' : text;
    }

    // --- EVENT LISTENERS ---
    
    // 1. Scroll ke bawah saat load
    document.addEventListener('DOMContentLoaded', scrollToBottom);

    // 2. Kirim Pesan
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const text = messageInput.value.trim();
            if (!text) return;

            // Kosongkan input dulu biar responsif
            messageInput.value = '';

            try {
                const res = await fetch(`/chat/${activeConversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ body: text })
                });
                
                if (res.ok) {
                    const msg = await res.json();
                    appendMessage(msg);
                    
                    // Trigger update sidebar manual agar chat naik ke atas seketika
                    fetchConversations(); 
                }
            } catch (err) {
                console.error('Gagal kirim pesan:', err);
            }
        });
    }

    // 3. Search Sidebar
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            document.querySelectorAll('.conv-item').forEach(el => {
                const name = el.querySelector('.name-el').innerText.toLowerCase();
                el.style.display = name.includes(val) ? 'block' : 'none';
            });
        });
    }

    // --- POLLING MESSAGES (Loop A: Chat Aktif) ---
    // Hanya jalan jika ada chat yang dibuka
    function appendMessage(msg) {
        if (!msgContainer) return;
        const isMe = msg.sender_id === CURRENT_USER_ID;
        const div = document.createElement('div');
        div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
        div.setAttribute('data-message-id', msg.id);
        
        // Format waktu JS jika tidak dari server
        const timeStr = msg.formatted_time || new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        div.innerHTML = `
            <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow ${isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'}">
                <p class="text-sm">${escapeHtml(msg.body)}</p>
                <span class="text-[10px] block text-right mt-1 ${isMe ? 'text-blue-200' : 'text-gray-400'}">
                    ${timeStr}
                </span>
            </div>
        `;
        msgContainer.appendChild(div);
        lastMessageId = msg.id;
        scrollToBottom();
    }

    async function pollMessages() {
        if (!activeConversationId) return;
        
        try {
            // Kita kirim request. Karena user sedang membuka chat ini, 
            // Controller akan otomatis menandai pesan yang diambil sebagai 'read'.
            const res = await fetch(`/chat/${activeConversationId}/fetch?last_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const msgs = await res.json();
                if (msgs.length > 0) {
                    msgs.forEach(m => appendMessage(m));
                    // Jika ada pesan masuk, update sidebar juga agar preview berubah
                    fetchConversations();
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    // --- POLLING SIDEBAR (Loop B: Daftar Kontak & Notifikasi) ---
    async function fetchConversations() {
        try {
            const res = await fetch('{{ route("chat.api.conversations") }}');
            if (!res.ok) return;
            const data = await res.json();
            
            updateSidebarUI(data);
        } catch (err) {
            console.error('Sidebar poll error:', err);
        }
    }

    function updateSidebarUI(conversations) {
        if (!conversationsList) return;

        // Jika data kosong
        if (conversations.length === 0) {
            conversationsList.innerHTML = '<div class="text-center mt-4 text-gray-400 text-sm">Belum ada chat</div>';
            return;
        }

        // 1. Loop data dari server (sudah terurut updated_at terbaru di controller)
        conversations.forEach((chat, index) => {
            let item = document.getElementById(`conv-item-${chat.id}`);
            
            // Tentukan status aktif
            const isActive = (chat.id === activeConversationId);
            
            // Logic Badge: Jika chat ini sedang aktif, paksa unread jadi 0
            // Jika tidak, pakai data dari server
            const unreadCount = isActive ? 0 : chat.unread_count;

            // HTML content untuk item ini
            const htmlContent = `
                <a href="/chat/${chat.id}" 
                   class="flex items-center p-3 rounded-lg transition ${isActive ? 'bg-blue-100 border-l-4 border-blue-600' : 'hover:bg-gray-100'}">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center text-white font-bold relative">
                        ${chat.partner_initial}
                        <span class="unread-badge absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold ${unreadCount > 0 ? '' : 'hidden'}">
                            ${unreadCount > 9 ? '9+' : unreadCount}
                        </span>
                    </div>
                    <div class="ml-3 overflow-hidden w-full">
                        <div class="flex justify-between items-baseline">
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-900 truncate name-el">${escapeHtml(chat.partner_name)}</span>
                            </div>
                            <span class="text-xs text-gray-500 time-el">${chat.last_message_time}</span>
                        </div>
                        <p class="text-sm text-gray-600 truncate msg-preview-el">
                           ${escapeHtml(truncate(chat.last_message || '', 35))}
                        </p>
                    </div>
                </a>
            `;

            // Jika elemen belum ada, buat baru
            if (!item) {
                item = document.createElement('div');
                item.id = `conv-item-${chat.id}`;
                item.className = 'conv-item';
                item.setAttribute('data-chat-id', chat.id);
                item.innerHTML = htmlContent;
                conversationsList.appendChild(item); // Append sementara, nanti di-sort
            } else {
                // Jika sudah ada, update isinya
                item.innerHTML = htmlContent;
            }

            // --- SORTING / REORDERING DOM ---
            // Kita ingin urutan DOM sesuai dengan urutan array 'conversations' (yang sudah disort server)
            // 'conversationsList.children[index]' adalah elemen yang seharusnya ada di posisi ini.
            const currentItemAtPos = conversationsList.children[index];
            
            // Jika elemen di posisi ini bukan elemen yang seharusnya (berdasarkan ID), maka pindahkan/insert
            if (currentItemAtPos !== item) {
                // insertBefore akan memindahkan item jika sudah ada di DOM
                conversationsList.insertBefore(item, currentItemAtPos);
            }
        });
    }

    // --- JALANKAN INTERVAL ---
    // 1. Poll Pesan (Cepat: 1.5 detik) -> hanya jika ada chat aktif
    if (activeConversationId) {
        setInterval(pollMessages, 1500);
    }

    // 2. Poll Sidebar (Sedang: 3 detik) -> update badge & urutan
    setInterval(fetchConversations, 3000);


// ... Variable lama ...
    const recordBtn = document.getElementById('recordBtn');
    const recordingIndicator = document.getElementById('recordingIndicator');
    
    let mediaRecorder;
    let audioChunks = [];
    let isRecording = false;

    // 1. LOGIKA TOMBOL MIC
    if (recordBtn) {
        recordBtn.addEventListener('click', async () => {
            if (!isRecording) {
                // START RECORDING
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];

                    mediaRecorder.ondataavailable = event => {
                        audioChunks.push(event.data);
                    };

                    mediaRecorder.onstop = async () => {
                        // Proses kirim saat stop
                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                        sendVoiceNote(audioBlob);
                        
                        // Matikan track microphone
                        stream.getTracks().forEach(track => track.stop());
                    };

                    mediaRecorder.start();
                    isRecording = true;
                    
                    // UI Updates
                    recordBtn.classList.remove('bg-gray-200', 'text-gray-700');
                    recordBtn.classList.add('bg-red-600', 'text-white', 'animate-pulse');
                    recordingIndicator.classList.remove('hidden');
                    messageInput.placeholder = "Sedang merekam (klik mic lagi untuk kirim)...";
                    messageInput.disabled = true;

                } catch (err) {
                    alert('Gagal mengakses microphone: ' + err);
                }
            } else {
                // STOP RECORDING & SEND
                mediaRecorder.stop();
                isRecording = false;

                // UI Reset
                recordBtn.classList.add('bg-gray-200', 'text-gray-700');
                recordBtn.classList.remove('bg-red-600', 'text-white', 'animate-pulse');
                recordingIndicator.classList.add('hidden');
                messageInput.placeholder = "Tulis pesan...";
                messageInput.disabled = false;
            }
        });
    }

    // 2. FUNGSI KIRIM VOICE NOTE (AJAX)
    async function sendVoiceNote(blob) {
        const formData = new FormData();
        formData.append('audio_blob', blob, 'voice-note.webm');
        // formData.append('body', '🎤 Voice Note'); // Optional

        try {
            const res = await fetch(`/chat/${activeConversationId}/send`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                    // Jangan set Content-Type header jika pakai FormData, browser akan set otomatis
                },
                body: formData
            });

            if (res.ok) {
                const msg = await res.json();
                appendMessage(msg);
                fetchConversations();
            }
        } catch (err) {
            console.error('Gagal kirim VN:', err);
        }
    }

    // 3. UPDATE FUNGSI APPENDMESSAGE (Agar bisa munculin Audio Player)
    function appendMessage(msg) {
        if (!msgContainer) return;
        const isMe = msg.sender_id === CURRENT_USER_ID;
        const div = document.createElement('div');
        div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
        div.setAttribute('data-message-id', msg.id);
        
        const timeStr = msg.formatted_time || new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        // Tentukan Isi Konten (Text atau Audio)
        let contentHtml = '';
        if (msg.type === 'audio' && msg.attachment) {
            // Render Audio Player
            // Pastikan path attachment benar (storage/voice_notes/...)
            const audioUrl = `/storage/${msg.attachment}`; 
            contentHtml = `
                <audio controls class="max-w-[200px] h-8 mt-1">
                    <source src="${audioUrl}" type="audio/webm">
                    <source src="${audioUrl}" type="audio/ogg">
                    Browser tidak support audio.
                </audio>
            `;
        } else {
            // Render Text Biasa
            contentHtml = `<p class="text-sm">${escapeHtml(msg.body)}</p>`;
        }

        div.innerHTML = `
            <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow ${isMe ? 'bg-blue-600 text-white rounded-br-none' : 'bg-white text-gray-800 rounded-bl-none border'}">
                ${contentHtml}
                <span class="text-[10px] block text-right mt-1 ${isMe ? 'text-blue-200' : 'text-gray-400'}">
                    ${timeStr}
                </span>
            </div>
        `;
        msgContainer.appendChild(div);
        lastMessageId = msg.id;
        scrollToBottom();
    }
</script>
@endsection