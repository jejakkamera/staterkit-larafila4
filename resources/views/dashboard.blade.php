<x-layouts.app :title="__('Dashboard')">
    <div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-2">
                <h2 class="text-2xl font-semibold text-zinc-900 dark:text-white">{{ __('Active Users') }}</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Users active in the last 15 minutes.') }}
                </p>
            </div>

            <div class="mt-6 flex items-end justify-between">
                <div class="text-5xl font-bold text-zinc-900 dark:text-white">
                    {{ $activeCount }}
                </div>
                <div class="text-right text-xs text-zinc-500 dark:text-zinc-400">
                    <p>{{ __('Last updated :time', ['time' => $lastUpdated->format('H:i')]) }}</p>
                    <p>{{ $lastUpdated->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
