<div class="w-full max-w-2xl mx-auto p-6">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg">
        <!-- Header -->
        <div class="border-b border-zinc-200 dark:border-zinc-700 p-4">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">💬 OpenAI Chat</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Powered by GPT-3.5 Turbo</p>
        </div>

        <!-- Messages Container -->
        <div class="h-96 overflow-y-auto p-4 space-y-4 bg-zinc-50 dark:bg-zinc-800">
            @if (empty($messages))
                <div class="flex items-center justify-center h-full">
                    <div class="text-center text-zinc-500 dark:text-zinc-400">
                        <p class="text-lg">Mulai percakapan dengan AI</p>
                        <p class="text-sm">Ketik pesan Anda di bawah</p>
                    </div>
                </div>
            @else
                @foreach ($messages as $message)
                    @if ($message['role'] === 'user')
                        <div class="flex justify-end">
                            <div class="bg-blue-500 text-white rounded-lg px-4 py-2 max-w-xs">
                                {{ $message['content'] }}
                            </div>
                        </div>
                    @elseif ($message['role'] === 'assistant')
                        <div class="flex justify-start">
                            <div class="bg-zinc-200 dark:bg-zinc-700 text-zinc-900 dark:text-white rounded-lg px-4 py-2 max-w-xs">
                                {{ $message['content'] }}
                            </div>
                        </div>
                    @else
                        <div class="flex justify-center">
                            <div class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded-lg px-4 py-2">
                                {{ $message['content'] }}
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            @if ($isLoading)
                <div class="flex justify-start">
                    <div class="bg-zinc-200 dark:bg-zinc-700 rounded-lg px-4 py-2">
                        <div class="flex space-x-2">
                            <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-zinc-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Input Area -->
        <div class="border-t border-zinc-200 dark:border-zinc-700 p-4">
            <form wire:submit="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    wire:model="userMessage"
                    placeholder="Ketik pesan Anda..."
                    class="flex-1 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg dark:bg-zinc-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    {{ $isLoading ? 'disabled' : '' }}
                />
                <button
                    type="submit"
                    {{ $isLoading ? 'disabled' : '' }}
                    class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-400 transition"
                >
                    {{ $isLoading ? 'Mengirim...' : 'Kirim' }}
                </button>
            </form>

            @if (!empty($messages))
                <div class="mt-3">
                    <button
                        wire:click="clearChat"
                        class="text-sm px-3 py-1 bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded hover:bg-zinc-300 dark:hover:bg-zinc-600 transition"
                    >
                        Hapus Chat
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
