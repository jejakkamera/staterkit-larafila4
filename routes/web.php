<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', \App\Livewire\Chat::class)->name('chat');
    
    // Impersonate routes - accessible by any authenticated user
    Route::post('/impersonate/switch-back', function () {
        $originalAdminId = session('original_admin_id');
        if ($originalAdminId) {
            $admin = \App\Models\User::find($originalAdminId);
            if ($admin) {
                \Illuminate\Support\Facades\Auth::login($admin);
                session()->forget(['original_admin_id', 'impersonating']);
            }
        }
        return redirect()->route('admin.users');
    })->name('impersonate.switch-back');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::redirect('admin', 'dashboard');
    Route::get('/admin/dashboard', \App\Livewire\AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', \App\Livewire\Admin\UserList::class)->name('admin.users');
    Route::get('/admin/settings', \App\Livewire\Admin\WebsiteSettings::class)->name('admin.settings');
    Route::get('/admin/backup', \App\Livewire\Admin\DatabaseBackup::class)->name('admin.backup');
});
