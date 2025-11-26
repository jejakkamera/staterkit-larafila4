<div class="space-y-6 p-8">
    <div class="flex flex-col gap-2">
        <h1 class="text-3xl font-semibold text-zinc-900 dark:text-white">{{ __('System Logs') }}</h1>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Inspect recent Laravel error logs and monitor issues in real time.') }}</p>
    </div>

    <div class="grid gap-4 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-6">
        <div class="lg:col-span-2">
            <label class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Log file') }}</label>
            <select wire:model="selectedLog" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                @forelse ($logFiles as $file)
                    <option value="{{ $file }}">{{ $file }}</option>
                @empty
                    <option value="">{{ __('No log files found') }}</option>
                @endforelse
            </select>
        </div>

        <div class="lg:col-span-2">
            <label class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Search') }}</label>
            <input
                type="text"
                wire:model.debounce.500ms="search"
                placeholder="{{ __('message, level, context...') }}"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
            />
        </div>

        <div>
            <label class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Lines to show') }}</label>
            <select wire:model="lineLimit" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="500">500</option>
                <option value="1000">1000</option>
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('Start date') }}</label>
            <input
                type="date"
                wire:model="startDate"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
            />
        </div>

        <div>
            <label class="text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ __('End date') }}</label>
            <input
                type="date"
                wire:model="endDate"
                class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-800"
            />
        </div>

        <div class="flex flex-col gap-2 lg:col-span-6 lg:flex-row lg:items-end">
            <div class="flex flex-1 flex-wrap gap-2">
                <flux:button type="button" wire:click="refreshLogs" icon="arrow-path" icon:class="opacity-80" wire:loading.attr="disabled">{{ __('Refresh') }}</flux:button>
                <flux:button type="button" wire:click="download" variant="outline" icon="arrow-down-tray" icon:class="opacity-80" wire:loading.attr="disabled">{{ __('Download log') }}</flux:button>
                <flux:button type="button" wire:click="resetLog" variant="ghost" icon="arrow-uturn-left" icon:class="opacity-80" wire:loading.attr="disabled">{{ __('Reset log') }}</flux:button>
                <flux:button
                    type="button"
                    variant="outline"
                    class="text-red-600 dark:text-red-400 border-red-200 dark:border-red-500/40"
                    icon="trash"
                    icon:class="opacity-80"
                    wire:click="deleteLog"
                    wire:loading.attr="disabled"
                    onclick="if(!confirm('{{ __('Delete this log file? This cannot be undone.') }}')){event.stopImmediatePropagation();event.preventDefault();}"
                >
                    {{ __('Delete log') }}
                </flux:button>
            </div>
            @if ($statusMessage)
                <span class="text-xs text-green-600 dark:text-green-400">{{ $statusMessage }}</span>
            @endif
            <div wire:loading.flex class="items-center gap-2 text-xs text-zinc-500">
                <span class="inline-block h-3 w-3 animate-spin rounded-full border border-zinc-400 border-t-transparent"></span>
                <span>{{ __('Loading log data...') }}</span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
            <span>{{ __('Showing latest :lines lines from :file', ['lines' => $lineLimit, 'file' => $selectedLog]) }}</span>
            <span>{{ now()->format('H:i:s') }}</span>
        </div>

        @if (empty($entries))
            <p class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">{{ __('No log entries found for the current selection.') }}</p>
        @else
            <div class="max-h-[600px] overflow-y-auto px-6 py-4">
                <div class="space-y-3 text-sm">
                    @foreach ($entries as $entry)
                        @php
                            $levelClass = match ($entry['level']) {
                                'error' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                                'warning' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                                'debug' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200',
                                default => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                            };
                        @endphp
                        <div class="rounded-xl border border-zinc-100 p-4 dark:border-zinc-800">
                            <div class="flex flex-wrap items-center gap-3 text-xs">
                                <span class="font-mono text-zinc-500">{{ $entry['timestamp'] ?? __('N/A') }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize {{ $levelClass }}">
                                    {{ $entry['level'] }}
                                </span>
                                @if (!empty($entry['channel']))
                                    <span class="text-zinc-400">{{ $entry['channel'] }}</span>
                                @endif
                            </div>
                            <pre class="mt-3 whitespace-pre-wrap font-mono text-xs text-zinc-800 dark:text-zinc-100">{{ $entry['message'] }}</pre>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex flex-col gap-3 border-t border-zinc-200 px-6 py-4 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <span>{{ __('Page :page', ['page' => $page]) }}</span>
                @if ($hasMore)
                    <span>• {{ __('More results available') }}</span>
                @else
                    <span>• {{ __('End of results') }}</span>
                @endif
            </div>
            <div class="flex gap-2">
                <flux:button type="button" variant="outline" icon="arrow-left" icon:class="opacity-80" wire:click="previousPage" :disabled="$page === 1" wire:loading.attr="disabled">
                    {{ __('Previous') }}
                </flux:button>
                <flux:button type="button" variant="outline" icon="arrow-right" icon:class="opacity-80" wire:click="nextPage" :disabled="!$hasMore" wire:loading.attr="disabled">
                    {{ __('Next') }}
                </flux:button>
            </div>
        </div>
    </div>
</div>
