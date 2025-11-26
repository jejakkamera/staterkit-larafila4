<x-layouts.app :title="__('Accordion Demo')">
    <flux:card class="space-y-6">
        <div class="flex flex-col gap-1">
            <flux:heading size="lg">Accordion (Flux Pro)</flux:heading>
            <flux:text class="text-zinc-500 dark:text-zinc-400">
                Komponen resmi Flux Pro untuk FAQ atau blok konten collapsible. Contoh di bawah
                memperlihatkan kombinasi state default terbuka, ikon custom, dan transisi.
            </flux:text>
        </div>

        <flux:accordion class="space-y-2" transition>
            <flux:accordion.item heading="Bagaimana cara upgrade Flux Pro?" :expanded="true">
                <p>
                    Jalankan <code>composer remove livewire/flux-pro</code>, ganti file ZIP di folder
                    <code>packages/</code>, lalu <code>composer require livewire/flux-pro:VERSI livewire/flux:VERSI -W</code>.
                </p>
            </flux:accordion.item>

            <flux:accordion.item heading="Apakah mendukung Livewire v4?">
                <p>
                    Ya, mulai Flux 2.6.1 sudah kompatibel dengan Livewire v4 dan komponen Blade-nya otomatis
                    bekerja setelah menjalankan <code>@fluxScripts</code> di layout.
                </p>
            </flux:accordion.item>

            <flux:accordion.item heading="Bisakah saya menaruh konten kompleks?">
                <div class="space-y-3">
                    <flux:text>Pasti, bahkan tombol dan badge sekalipun.</flux:text>
                    <flux:button variant="outline" size="sm" icon="sparkles">Contoh aksi</flux:button>
                </div>
            </flux:accordion.item>
        </flux:accordion>

        <flux:accordion variant="reverse">
            <flux:accordion.item>
                <flux:accordion.heading>
                    <div class="flex flex-col">
                        <flux:heading size="sm">Status subscription</flux:heading>
                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Menampilkan indikator di sebelah kanan.</flux:text>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    <flux:badge color="lime" size="sm">Aktif sejak 2024</flux:badge>
                </flux:accordion.content>
            </flux:accordion.item>

            <flux:accordion.item>
                <flux:accordion.heading>
                    <div class="flex flex-col">
                        <flux:heading size="sm">Jadwal maintenance</flux:heading>
                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">Khusus admin.</flux:text>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    <flux:text>Sabtu, 30 November 22:00 - 23:59 WIB.</flux:text>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    </flux:card>
</x-layouts.app>
