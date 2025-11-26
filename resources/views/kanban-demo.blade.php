<x-layouts.app :title="__('Kanban Demo')">
    <flux:card class="space-y-4">
        <flux:heading size="lg">Komponen Kanban belum tersedia di paket ini</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400">
            Paket Flux Pro yang terpasang sekarang (2.6.1) belum menyertakan view <code>flux/kanban</code>, sehingga tag seperti <code>&lt;flux:kanban.column.header&gt;</code> tidak dikenal oleh Blade.
        </flux:text>

        <div class="space-y-1 text-sm text-zinc-600 dark:text-zinc-300">
            <div class="flex items-start gap-2">
                <flux:icon name="arrow-path" class="mt-0.5" />
                <p>Hubungi penjual untuk mendapatkan rilis Flux Pro terbaru yang sudah memiliki folder <code>stubs/resources/views/flux/kanban</code>.</p>
            </div>
            <div class="flex items-start gap-2">
                <flux:icon name="archive-box" class="mt-0.5" />
                <p>Setelah mendapatkan versi baru, hapus paket lama (<code>composer remove livewire/flux-pro</code>), ganti file zip di <code>packages/</code>, lalu jalankan <code>composer require livewire/flux-pro:VERSI livewire/flux:VERSI -W</code>.</p>
            </div>
            <div class="flex items-start gap-2">
                <flux:icon name="sparkles" class="mt-0.5" />
                <p>Jika halaman ini masih muncul setelah upgrade, jalankan <code>php artisan view:clear</code> dan refresh untuk memuat ulang Blade view terbaru.</p>
            </div>
        </div>

        <flux:callout icon="information-circle">
            Selama menunggu update, kamu tetap bisa memanfaatkan komponen pro lain yang sudah tersedia (date picker, editor, chart, dsb.) di dashboard.
        </flux:callout>
    </flux:card>
</x-layouts.app>
