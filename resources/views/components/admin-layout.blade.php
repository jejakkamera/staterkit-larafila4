<div class="w-full">
    <!-- Admin Sidebar Navigation -->
    <div class="flex h-screen bg-zinc-100 dark:bg-zinc-900">
        <!-- Sidebar -->
        <div class="w-64 bg-white dark:bg-zinc-800 shadow">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">🛡️ Admin Panel</h2>
            </div>

            <nav class="p-4 space-y-2">
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-zinc-700 transition font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 dark:bg-zinc-700 text-blue-600 dark:text-blue-400' : '' }}"
                >
                    📊 Dashboard
                </a>
                <a
                    href="{{ route('admin.users') }}"
                    class="block px-4 py-2 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-zinc-700 transition font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-50 dark:bg-zinc-700 text-blue-600 dark:text-blue-400' : '' }}"
                >
                    👥 User Management
                </a>
                <a
                    href="{{ route('dashboard') }}"
                    class="block px-4 py-2 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-blue-50 dark:hover:bg-zinc-700 transition font-medium"
                >
                    ↩️ Back to Dashboard
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto p-6">
            {{ $slot }}
        </div>
    </div>
</div>
