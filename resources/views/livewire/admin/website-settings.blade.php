<div class="space-y-8 p-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white">{{ __('Website Settings') }}</h1>
        <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">{{ __('Manage your website configuration') }}</p>
    </div>

    <!-- Filament Form -->
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <!-- Submit Button -->
        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">
                {{ __('Save Settings') }}
            </flux:button>
        </div>
    </form>
</div>
