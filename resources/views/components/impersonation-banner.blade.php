@if (session('impersonating'))
<div class="fixed top-0 left-0 right-0 bg-yellow-100 border-b-2 border-yellow-400 dark:bg-yellow-900/30 dark:border-yellow-600 px-4 py-3 flex items-center justify-between gap-4 z-50" style="top: 0;">
    <div class="flex items-center gap-3 flex-1">
        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
            You are logged in as <strong>{{ auth()->user()->name }}</strong>. This is a test session.
        </span>
    </div>
    <form method="POST" action="{{ route('impersonate.switch-back') }}" class="flex-shrink-0" style="pointer-events: auto;">
        @csrf
        <button type="submit" class="px-3 py-1 bg-yellow-600 dark:bg-yellow-700 text-white text-sm font-medium rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition-colors" style="pointer-events: auto; cursor: pointer;">
            Switch Back
        </button>
    </form>
</div>
@endif
