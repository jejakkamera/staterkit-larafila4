<div class="max-w-7xl mx-auto" x-data="{ 
    pollingInterval: null,
    openMcpModal() {
        window.openAIModal();
    }
}" @load="
    if ($wire.isBackingUp && $wire.activeBackupProgress) {
        pollingInterval = setInterval(() => {
            $wire.dispatch('poll-backup');
        }, 2000);
    }
" @backup-complete="clearInterval(pollingInterval)">
    <!-- Breadcrumb Navigation -->
    <div class="mb-6 flex items-center space-x-2 text-sm">
        <a href="/" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">Home</a>
        <span class="text-zinc-400 dark:text-zinc-600">/</span>
        <a href="/dashboard" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">Dashboard</a>
        <span class="text-zinc-400 dark:text-zinc-600">/</span>
        <span class="text-zinc-900 dark:text-white font-medium">Database Backup</span>
    </div>

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-2">Database Backup</h1>
        <p class="text-lg text-zinc-600 dark:text-zinc-400">Create and manage database backups</p>
        <div class="h-1 w-24 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mt-4"></div>
    </div>

    <!-- Status Message -->
    @if($backupMessage)
        <div class="mb-6 p-4 rounded-lg border {{ $backupStatus === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-200' : ($backupStatus === 'error' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-200' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200') }}">
            {{ $backupMessage }}
        </div>
    @endif

    <!-- Active Backup Progress -->
    @if($isBackingUp && $activeBackupProgress)
        <div class="mb-8 bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                <span class="inline-block animate-spin mr-2">⏳</span>
                Backup in Progress...
            </h2>
            
            <div class="mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $activeBackupProgress->message }}</span>
                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $activeBackupProgress->progress }}%</span>
                </div>
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-full transition-all duration-300" style="width: {{ $activeBackupProgress->progress }}%;"></div>
                </div>
            </div>

            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Started at: {{ $activeBackupProgress->started_at->format('H:i:s') }}
            </p>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="mb-8 flex flex-wrap gap-4">
        <button 
            wire:click="createBackup"
            wire:loading.attr="disabled"
            class="px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            title="Create a new database backup"
        >
            <span wire:loading.remove>
                <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                </svg>
                Create Backup Now
            </span>
            <span wire:loading>
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Queuing Backup...
            </span>
        </button>

        <button 
            @click="openMcpModal()"
            class="px-4 py-3 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold rounded-lg border-2 border-blue-300 dark:border-blue-700 hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all flex items-center gap-2"
            title="Open AI Assistant for backup management"
        >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
            </svg>
            <span>Ask AI</span>
        </button>
    </div>

    <!-- Info Box -->
    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <p class="text-sm text-blue-800 dark:text-blue-200">
            <strong>ℹ️ Note:</strong> Backups are processed in the background queue. You can close this page and the backup will continue. Check back later to download your backup.
        </p>
    </div>

    <!-- Backups List -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Available Backups</h2>
        </div>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                            <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">Filename</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">Size</th>
                            <th class="px-6 py-3 text-left font-semibold text-zinc-900 dark:text-white">Date</th>
                            <th class="px-6 py-3 text-center font-semibold text-zinc-900 dark:text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 text-zinc-900 dark:text-white font-mono text-xs">{{ $backup['name'] }}</td>
                                <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ $backup['humanDate'] ?? $backup['size'] }}</td>
                                <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">{{ \Carbon\Carbon::createFromTimestamp($backup['date'])->format('Y-m-d H:i:s') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <button 
                                            wire:click="downloadBackup('{{ $backup['name'] }}')"
                                            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded transition-colors"
                                            title="Download this backup"
                                        >
                                            Download
                                        </button>
                                        <button 
                                            wire:click="deleteBackup('{{ $backup['name'] }}')"
                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded transition-colors"
                                            title="Delete this backup"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 mx-auto mb-3 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-zinc-500 dark:text-zinc-400 font-medium">No backups available yet</p>
                <p class="text-zinc-400 dark:text-zinc-500 text-sm mt-1">Create your first backup using the button above</p>
            </div>
        @endif
    </div>

    <!-- Info Text -->
    <div class="mt-8 space-y-4 text-sm text-zinc-600 dark:text-zinc-400">
        <p>
            <strong>Database:</strong> {{ env('DB_DATABASE', 'Not configured') }}
        </p>
        <p>
            <strong>Note:</strong> Backups are stored in the storage/app/backups directory. Make sure your server has MySQL installed and the DB_HOST, DB_USERNAME, DB_PASSWORD, and DB_DATABASE environment variables are properly configured. Backup files are automatically compressed with gzip to save space.
        </p>
    </div>

    <!-- MCP AI Assistant Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" id="mcpModalContainer">
        <div 
            x-data="{ 
                mcpModalOpen: false, 
                mcpMessages: @json($mcpMessages ?? []),
                mcpLoading: false
            }" 
            @open-mcp-modal-trigger.window="mcpModalOpen = true; document.getElementById('mcpModalContainer').style.display = 'flex'"
            @close-mcp-modal.window="mcpModalOpen = false; document.getElementById('mcpModalContainer').style.display = 'none'"
            @mcp-response-received.window="window.handleMcpResponse(mcpMessages, $event); mcpLoading = false; window.scrollMcpChat()"
            @mcp-error.window="window.handleMcpError(mcpMessages, $event); mcpLoading = false"
            x-effect="mcpMessages = @json($mcpMessages ?? [])"
            class="w-full h-full flex items-center justify-center"
        >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50" @click="window.closeMcpModal()"></div>
        
        <!-- Modal -->
        <div 
            class="relative w-full max-w-2xl max-h-[80vh] bg-white dark:bg-slate-900 rounded-lg shadow-2xl flex flex-col border border-gray-200 dark:border-slate-700 overflow-hidden"
            @click.stop
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">AI Assistant</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Database Backup Manager</p>
                </div>
                <button 
                    @click="mcpModalOpen = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Chat Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="mcpChatContainer" wire:ignore.self style="min-height: 200px; max-height: calc(80vh - 180px);" x-data="{ 
                get messages() { return window.mcpMessages || []; }
            }" x-effect="$nextTick(() => { window.scrollMcpChat(); })">
                <template x-if="mcpMessages.length === 0">
                    <div class="space-y-3 text-center py-4">
                        <p class="text-sm font-semibold text-gray-600 dark:text-slate-300">Start a conversation</p>
                        <div class="text-xs text-gray-500 dark:text-slate-400 space-y-2">
                            <p>Try asking:</p>
                            <ul class="space-y-1">
                                <li>• Create a backup</li>
                                <li>• List all backups</li>
                                <li>• How much space do backups use?</li>
                            </ul>
                        </div>
                    </div>
                </template>

                <template x-for="(message, idx) in mcpMessages" :key="idx">
                    <template x-if="message.role === 'user'">
                        <div class="flex justify-end w-full">
                            <div class="bg-blue-600 text-white max-w-md px-4 py-2 rounded-lg text-sm break-words">
                                <p x-text="message.content" class="whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="message.role !== 'user'">
                        <div class="flex justify-start w-full">
                            <div class="bg-gray-100 dark:bg-slate-800 text-gray-900 dark:text-white max-w-md px-4 py-2 rounded-lg text-sm break-words">
                                <p x-text="message.content" class="whitespace-pre-wrap"></p>
                            </div>
                        </div>
                    </template>
                </template>

                <template x-if="mcpLoading">
                    <div class="flex justify-start">
                        <div class="bg-gray-100 dark:bg-slate-800 px-4 py-2 rounded-lg">
                            <div class="flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Input Area -->
            <div class="border-t border-gray-200 dark:border-slate-700 p-4">
                <div class="flex gap-2">
                    <input 
                        type="text"
                        x-ref="mcpInput"
                        placeholder="Ask about backups..."
                        class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:text-white"
                        @keydown.enter="window.sendMcpMessage($refs.mcpInput, mcpMessages, mcpLoading, $wire)"
                    />
                    <button 
                        @click="window.sendMcpMessage($refs.mcpInput, mcpMessages, mcpLoading, $wire)"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition"
                    >
                        <template x-if="!mcpLoading">
                            <span>Send</span>
                        </template>
                        <template x-if="mcpLoading">
                            <svg class="w-4 h-4 inline animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            </svg>
                        </template>
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
// Global reference untuk akses modal dari button
window.openAIModal = function() {
    window.dispatchEvent(new CustomEvent('open-mcp-modal-trigger'));
};

// Close modal
window.closeMcpModal = function() {
    window.dispatchEvent(new CustomEvent('close-mcp-modal'));
};

// Handle MCP response
window.handleMcpResponse = function(mcpMessages, event) {
    mcpMessages.push({
        role: 'assistant',
        content: event.detail.response
    });
};

// Handle MCP error
window.handleMcpError = function(mcpMessages, event) {
    mcpMessages.push({
        role: 'error',
        content: 'Error: ' + event.detail.error
    });
};

// Scroll chat ke bawah
window.scrollMcpChat = function() {
    const container = document.getElementById('mcpChatContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
};

// Helper function untuk send MCP message
window.sendMcpMessage = function(inputElement, mcpMessages, mcpLoading, wire) {
    const input = inputElement.value.trim();
    if (!input || mcpLoading) return;
    
    mcpMessages.push({
        role: 'user',
        content: input
    });
    inputElement.value = '';
    
    window.scrollMcpChat();
    wire.call('handleMcpMessage', input);
};

// Listen for Livewire updates to mcpMessages
document.addEventListener('livewire:updated', function() {
    window.scrollMcpChat();
});
</script>
