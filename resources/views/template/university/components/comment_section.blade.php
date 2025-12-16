@props(['post', 'comments'])

@php
    $commentsEnabled = \App\Models\SiteSetting::getValue('enable_comments') == '1';
    $recaptchaSiteKey = \App\Models\SiteSetting::getValue('recaptcha_site_key');
@endphp

@if($commentsEnabled)
    <div class="mt-12 bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8" id="comments-section">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-2">
            <i class="far fa-comments text-primary"></i>
            Komentar ({{ $comments->count() }})
        </h3>

        {{-- Comment Form --}}
        <div class="mb-12 bg-gray-50 rounded-xl p-6 border border-gray-200">
            <h4 class="text-lg font-bold text-gray-800 mb-4">Tulis Komentar</h4>
            <form id="commentForm" action="{{ route('frontend.comments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="Nama Anda">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                class="text-gray-400 text-xs">(Opsional)</span></label>
                        <input type="email" name="email" id="email"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="email@contoh.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Komentar <span
                            class="text-red-500">*</span></label>
                    <textarea name="content" id="content" rows="4" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                        placeholder="Tulis komentar Anda di sini..."></textarea>
                </div>

                {{-- Recaptcha --}}
                @if(!app()->isLocal() && !empty($recaptchaSiteKey))
                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                    </div>
                    @push('scripts')
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    @endpush
                @endif

                <div id="commentMessage" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                <div class="text-right">
                    <button type="submit" id="submitComment"
                        class="px-6 py-2 bg-primary text-white font-bold rounded-lg hover:bg-blue-800 transition-colors shadow-lg shadow-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="inline-block" id="btnText">Kirim Komentar</span>
                        <i class="fas fa-paper-plane ml-2"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Comments List --}}
        <div class="space-y-6">
            @forelse($comments as $comment)
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center shrink-0 text-gray-500 text-lg font-bold border border-gray-300">
                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 relative">
                            {{-- Triangle --}}
                            <div
                                class="absolute top-4 -left-2 w-4 h-4 bg-gray-50 border-l border-b border-gray-100 transform rotate-45">
                            </div>

                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div>
                                    <h5 class="font-bold text-gray-900">{{ $comment->name }}</h5>
                                    <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm leading-relaxed relative z-10">
                                {{ $comment->content }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <i class="far fa-comment-dots text-4xl mb-3 text-gray-300"></i>
                    <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- AJAX Script --}}
    @push('scripts')
        <script>
            document.getElementById('commentForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const form = this;
                const btn = document.getElementById('submitComment');
                const btnText = document.getElementById('btnText');
                const updateMsg = document.getElementById('commentMessage');

                // Disable button
                btn.disabled = true;
                btnText.textContent = 'Mengirim...';
                updateMsg.classList.add('hidden');

                try {
                    const formData = new FormData(form);

                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            // CSRF token is handled by the input field in FormData automatically? 
                            // No, creating FormData from form includes hidden inputs.
                        }
                    });

                    const data = await response.json();

                    updateMsg.classList.remove('hidden');

                    if (response.ok) {
                        updateMsg.classList.remove('bg-red-100', 'text-red-700');
                        updateMsg.classList.add('bg-green-100', 'text-green-700');
                        updateMsg.textContent = data.message;
                        form.reset();

                        // If auto-approved, maybe reload to show it? Or append it?
                        // For simplicity, reload after a delay
                        if (data.comment && data.comment.status === 'approved') {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else {
                        updateMsg.classList.remove('bg-green-100', 'text-green-700');
                        updateMsg.classList.add('bg-red-100', 'text-red-700');
                        updateMsg.textContent = data.message || 'Terjadi kesalahan.';

                        if (data.errors) {
                            let errorText = '';
                            for (const [key, value] of Object.entries(data.errors)) {
                                errorText += value[0] + ' ';
                            }
                            updateMsg.textContent = errorText;
                        }
                    }

                } catch (error) {
                    console.error('Error:', error);
                    updateMsg.classList.remove('hidden', 'bg-green-100', 'text-green-700');
                    updateMsg.classList.add('bg-red-100', 'text-red-700');
                    updateMsg.textContent = 'Terjadi kesalahan koneksi.';
                } finally {
                    btn.disabled = false;
                    btnText.textContent = 'Kirim Komentar';

                    // Reset recaptcha if exists
                    if (window.grecaptcha) {
                        grecaptcha.reset();
                    }
                }
            });
        </script>
    @endpush
@endif