<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-zinc-900 dark:text-white mb-2">🛡️ Admin Dashboard</h1>
        <p class="text-zinc-600 dark:text-zinc-400">Manage users and system settings</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="text-blue-500 text-4xl">👥</div>
            </div>
        </div>

        <!-- Admin Count Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium">Administrators</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $adminCount }}</p>
                </div>
                <div class="text-purple-500 text-4xl">🛡️</div>
            </div>
        </div>

        <!-- User Count Card -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm font-medium">Regular Users</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white mt-2">{{ $userCount }}</p>
                </div>
                <div class="text-green-500 text-4xl">👤</div>
            </div>
        </div>
    </div>

    <!-- Users Quick View -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Recent Users</h2>
            <a
                href="{{ route('admin.users') }}"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
                View All →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($users as $user)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">
                                        {{ $user->initials() }}
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($user->role === 'admin')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                        🛡️ Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        👤 User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->created_at->format('d M Y') }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
