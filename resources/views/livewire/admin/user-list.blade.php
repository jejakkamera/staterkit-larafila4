
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <p class="text-lg text-zinc-600 dark:text-zinc-400">Manage all system users and their roles</p>
            <div class="h-1 w-24 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full mt-4"></div>
        </div>

        <!-- Stats Row -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium mb-2">Total Users</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium mb-2">Administrators</p>
                <p class="text-4xl font-bold text-red-600 dark:text-red-400">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium mb-2">Regular Users</p>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400">{{ \App\Models\User::where('role', 'user')->count() }}</p>
            </div>
        </div>

        <!-- Table Card -->
        
                {{ $this->table }}

    <style>
        .fi-table {
            @apply w-full;
        }

        .fi-table th {
            @apply bg-zinc-100 dark:bg-zinc-800 px-6 py-3 text-left text-sm font-semibold text-zinc-900 dark:text-white border-b border-zinc-200 dark:border-zinc-700;
        }

        .fi-table td {
            @apply px-6 py-4 border-b border-zinc-200 dark:border-zinc-700;
        }

        .fi-table tbody tr {
            @apply hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors;
        }

        .fi-table tbody tr:nth-child(even) {
            @apply bg-zinc-50 dark:bg-zinc-900/30;
        }
    </style>
</div>

