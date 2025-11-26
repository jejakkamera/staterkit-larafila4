<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                @php
                    $logo = \App\Helpers\WebsiteHelper::getLogo();
                @endphp
                @if ($logo)
                    <img src="{{ $logo }}" alt="Logo" class="h-8 w-auto">
                @else
                    <x-app-logo />
                @endif
                <span class="font-bold text-lg">{{ \App\Helpers\WebsiteHelper::getWebsiteName() }}</span>
            </a>

            <div class="px-3 py-2">
                <input 
                    type="text" 
                    id="sidebarSearch"
                    placeholder="Search..." 
                    class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm placeholder-zinc-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-800 dark:placeholder-zinc-500 dark:focus:border-blue-400 dark:focus:ring-blue-400"
                />
            </div>

            <flux:navlist id="sidebarNav" variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid" data-group="platform">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate data-search="dashboard home">{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>

                @if (auth()->user()->isAdmin())
                        @include('components.layouts.app.menus.admin', ['section' => 'nav'])
                @else
                    @include('components.layouts.app.menus.user')
                @endif
            </flux:navlist>

                @if (auth()->user()->isAdmin())
                    @include('components.layouts.app.menus.admin', ['section' => 'maintenance'])
                @endif

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <div class="relative inline-block">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon:trailing="chevrons-up-down"
                        data-test="sidebar-menu-button"
                    />
                    @if (session('impersonating'))
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            !
                        </span>
                    @endif
                </div>

                <flux:menu class="w-[200px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-xs font-normal">
                            <div class="flex items-center gap-1.5 px-1 py-1 text-start text-xs">
                                <span class="relative flex h-7 w-7 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white text-xs"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                    @if (session('impersonating'))
                                        <span class="absolute bottom-0 right-0 inline-flex h-2.5 w-2.5 rounded-full bg-red-600 ring-0.5 ring-white"></span>
                                    @endif
                                </span>

                                <div class="grid flex-1 text-start text-xs leading-tight">
                                    <span class="truncate font-semibold text-xs">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs opacity-75">{{ substr(auth()->user()->email, 0, 20) }}</span>
                                    @if (session('impersonating'))
                                        <span class="truncate text-xs font-semibold text-red-600 dark:text-red-400">TEST</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        @if (session('impersonating'))
                            <form method="POST" action="{{ route('impersonate.switch-back') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-left-start-on-rectangle" class="w-full">
                                    {{ __('Switch Back to Admin') }}
                                </flux:menu.item>
                            </form>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <div class="relative inline-block">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />
                    @if (session('impersonating'))
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            !
                        </span>
                    @endif
                </div>

                <flux:menu class="w-[180px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-xs font-normal">
                            <div class="flex items-center gap-1.5 px-1 py-1 text-start text-xs">
                                <span class="relative flex h-7 w-7 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white text-xs"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                    @if (session('impersonating'))
                                        <span class="absolute bottom-0 right-0 inline-flex h-2.5 w-2.5 rounded-full bg-red-600 ring-0.5 ring-white"></span>
                                    @endif
                                </span>

                                <div class="grid flex-1 text-start text-xs leading-tight">
                                    <span class="truncate font-semibold text-xs">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs opacity-75">{{ substr(auth()->user()->email, 0, 20) }}</span>
                                    @if (session('impersonating'))
                                        <span class="truncate text-xs font-semibold text-red-600 dark:text-red-400">Impersonat</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        @if (session('impersonating'))
                            <form method="POST" action="{{ route('impersonate.switch-back') }}" class="w-full">
                                @csrf
                                <flux:menu.item as="button" type="submit" icon="arrow-left-start-on-rectangle" class="w-full">
                                    {{ __('Switch Back to Admin') }}
                                </flux:menu.item>
                            </form>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('sidebarSearch');
                const sidebarNav = document.getElementById('sidebarNav');
                
                if (!searchInput || !sidebarNav) return;
                
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    const groups = sidebarNav.querySelectorAll('[data-group]');
                    const maintenanceGroups = document.querySelectorAll('[data-sidebar-group]');
                    
                    groups.forEach(group => {
                        const items = group.querySelectorAll('[data-search]');
                        let visibleCount = 0;
                        
                        items.forEach(item => {
                            const searchText = item.getAttribute('data-search').toLowerCase();
                            const matches = searchText.includes(query);
                            
                            if (query === '' || matches) {
                                item.style.display = '';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Hide group heading if no items match
                        group.style.display = visibleCount > 0 ? '' : 'none';
                    });

                    maintenanceGroups.forEach(group => {
                        const items = group.querySelectorAll('[data-search]');
                        let visibleCount = 0;

                        items.forEach(item => {
                            const searchText = item.getAttribute('data-search').toLowerCase();
                            const matches = searchText.includes(query);

                            if (query === '' || matches) {
                                item.style.display = '';
                                visibleCount++;
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        group.style.display = visibleCount > 0 ? '' : 'none';
                    });
                    
                    // Show Platform group always (Dashboard)
                    const platformGroup = sidebarNav.querySelector('[data-group="platform"]');
                    if (platformGroup && query === '') {
                        platformGroup.style.display = '';
                    }
                });
            });
        </script>

        @livewire('notifications')
        @fluxScripts
        @filamentScripts
    </body>
</html>
