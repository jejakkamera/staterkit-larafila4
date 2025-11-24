<flux:navlist.group :heading="__('Admin')" class="grid" data-group="admin">
    <flux:navlist.item icon="chart-bar" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate data-search="admin dashboard">{{ __('Admin Dashboard') }}</flux:navlist.item>
    <flux:navlist.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate data-search="user management users">{{ __('User Management') }}</flux:navlist.item>
    <flux:navlist.item icon="archive-box" :href="route('admin.backup')" :current="request()->routeIs('admin.backup')" wire:navigate data-search="database backup">{{ __('Database Backup') }}</flux:navlist.item>
    <flux:navlist.item icon="cog" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate data-search="website settings configuration">{{ __('Website Settings') }}</flux:navlist.item>
</flux:navlist.group>
