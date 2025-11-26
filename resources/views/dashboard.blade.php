<x-layouts.app :title="__('Dashboard')">
    @php
        $genres = [
            ['value' => 'fantasy', 'label' => 'Fantasy'],
            ['value' => 'science-fiction', 'label' => 'Science fiction'],
            ['value' => 'mystery', 'label' => 'Mystery'],
            ['value' => 'romance', 'label' => 'Romance'],
            ['value' => 'biography', 'label' => 'Biography'],
            ['value' => 'thriller', 'label' => 'Thriller'],
            ['value' => 'children', 'label' => 'Children'],
            ['value' => 'poetry', 'label' => 'Poetry'],
            ['value' => 'self-help', 'label' => 'Self help'],
        ];

        $perks = [
            ['value' => 'newsletter', 'label' => 'Newsletter', 'description' => 'Get the latest updates and offers.', 'checked' => true],
            ['value' => 'updates', 'label' => 'Product updates', 'description' => 'Learn about new features and releases.'],
            ['value' => 'events', 'label' => 'Event invitations', 'description' => 'Exclusive invites for launches.'],
        ];

        $activity = [
            ['label' => 'Sessions', 'value' => '42', 'trend' => '+18%', 'color' => 'lime', 'icon' => 'sparkles', 'meta' => 'Campaign shoots scheduled this week.'],
            ['label' => 'Approvals', 'value' => '12', 'trend' => '+4%', 'color' => 'sky', 'icon' => 'hand-thumb-up', 'meta' => 'Awaiting creative sign-off.'],
            ['label' => 'Tickets', 'value' => '7', 'trend' => '2 SLA', 'color' => 'amber', 'icon' => 'clock', 'meta' => 'Needing response within 24h.'],
        ];
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-2xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video rounded-2xl border border-neutral-200 bg-white/95 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="absolute inset-0 flex flex-col gap-4 p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <flux:heading size="sm">Studio schedule</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Sync releases & shoots</flux:text>
                        </div>

                        <flux:badge color="lime" size="xs" icon="bolt" inset="top bottom">Live</flux:badge>
                    </div>

                    <flux:date-picker mode="range" max-range="10" />

                    <flux:dropdown hover position="bottom" align="start" offset="-16" gap="10">
                        <button type="button" class="flex items-center gap-3">
                            <flux:avatar size="sm" name="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />

                            <div>
                                <flux:heading>Caleb Porzio</flux:heading>
                                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Creative Director</flux:text>
                            </div>
                        </button>

                        <flux:popover class="flex flex-col gap-3 rounded-2xl shadow-2xl">
                            <flux:avatar size="xl" name="Caleb Porzio" src="https://unavatar.io/x/calebporzio" />

                            <div>
                                <flux:heading size="lg">Caleb Porzio</flux:heading>

                                <div class="flex items-center gap-2">
                                    <flux:text size="lg">@calebporzio</flux:text>
                                    <flux:badge color="sky" size="sm">Follows you</flux:badge>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-300">
                                <flux:text class="flex gap-1"><flux:heading>775</flux:heading> following</flux:text>
                                <flux:text class="flex gap-1"><flux:heading>50.2k</flux:heading> followers</flux:text>
                            </div>

                            <div class="flex gap-2">
                                <flux:button variant="outline" size="sm" icon="check" icon:class="opacity-75" class="flex-1">Following</flux:button>
                                <flux:button variant="primary" size="sm" icon="chat-bubble-left-right" icon:class="opacity-75" class="flex-1">Message</flux:button>
                            </div>
                        </flux:popover>
                    </flux:dropdown>

                    <flux:dropdown>
                        <flux:button icon="chat-bubble-oval-left" icon:variant="micro" icon:class="text-zinc-300">
                            Feedback
                        </flux:button>

                        <flux:popover class="min-w-[28rem] flex flex-col gap-4">
                            <flux:radio.group variant="buttons" class="*:flex-1">
                                <flux:radio icon="bug-ant" checked>Bug report</flux:radio>
                                <flux:radio icon="light-bulb">Suggestion</flux:radio>
                                <flux:radio icon="question-mark-circle">Question</flux:radio>
                            </flux:radio.group>

                            <div class="relative">
                                <flux:textarea
                                    rows="6"
                                    class="dark:bg-transparent!"
                                    placeholder="Add reproduction steps or attach files."
                                />

                                <div class="absolute bottom-3 left-3 flex items-center gap-2">
                                    <flux:button variant="filled" size="xs" icon="face-smile" icon:variant="outline" icon:class="text-zinc-400 dark:text-zinc-300" />
                                    <flux:button variant="filled" size="xs" icon="paper-clip" icon:class="text-zinc-400 dark:text-zinc-300" />
                                </div>
                            </div>

                            <div class="flex gap-2 justify-end">
                                <flux:button variant="filled" size="sm" kbd="esc" class="w-24">Cancel</flux:button>
                                <flux:button size="sm" kbd="⏎" class="w-24">Submit</flux:button>
                            </div>
                        </flux:popover>
                    </flux:dropdown>
                </div>
            </div>

            <div class="relative aspect-video rounded-2xl border border-neutral-200 bg-white/95 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="absolute inset-0 flex flex-col gap-4 p-6">
                    <flux:heading size="sm">Content filters</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Choose the genres you want surfaced today.</flux:text>

                    <flux:checkbox.group label="Genres" variant="pills" class="max-h-[14rem] overflow-y-auto pr-1">
                        @foreach ($genres as $genre)
                            <flux:checkbox value="{{ $genre['value'] }}" label="{{ $genre['label'] }}" />
                        @endforeach
                    </flux:checkbox.group>

                    <flux:button variant="outline" size="sm" icon="sparkles">
                        Save preset
                    </flux:button>
                </div>
            </div>

            <div class="relative aspect-video rounded-2xl border border-neutral-200 bg-white/95 dark:border-neutral-700 dark:bg-zinc-900">
                <div class="absolute inset-0 flex flex-col gap-4 p-6">
                    <flux:heading size="sm">Session settings</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Switch between prepared rehearsals and live takes.</flux:text>

                    <flux:radio.group variant="buttons" class="grid grid-cols-2 gap-2">
                        <flux:radio icon="sparkles" checked>Creative</flux:radio>
                        <flux:radio icon="rocket-launch">Launch</flux:radio>
                        <flux:radio icon="beaker">Experiment</flux:radio>
                        <flux:radio icon="presentation-chart-line">Report</flux:radio>
                    </flux:radio.group>

                    <flux:checkbox.group variant="cards" class="grid gap-3">
                        <flux:checkbox checked
                            value="reminders"
                            label="Team reminders"
                            description="Send summary to collaborators every morning."
                        />
                        <flux:checkbox
                            value="backups"
                            label="Cloud backups"
                            description="Archive approved takes in cold storage."
                        />
                    </flux:checkbox.group>
                </div>
            </div>
        </div>

        <div class="relative h-full flex-1 rounded-2xl border border-neutral-200 bg-white/95 dark:border-neutral-700 dark:bg-zinc-900">
            <div class="absolute inset-0 flex flex-col gap-6 p-6">
                <div class="flex flex-col gap-6 lg:flex-row">
                    <flux:checkbox.group variant="cards" class="grid flex-1 gap-3 lg:grid-cols-3">
                        @foreach ($perks as $option)
                            <flux:checkbox
                                @if ($option['checked'] ?? false) checked @endif
                                value="{{ $option['value'] }}"
                                label="{{ $option['label'] }}"
                                description="{{ $option['description'] }}"
                            />
                        @endforeach
                    </flux:checkbox.group>

                    <div class="flex flex-col gap-3 lg:w-96">
                        <flux:heading size="sm">Creative notes</flux:heading>
                        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Capture lighting tweaks, prop swaps, or ideas for next takes.</flux:text>
                        <flux:editor class="**:data-[slot=content]:min-h-[160px]!" />
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    @foreach ($activity as $item)
                        <flux:card size="sm" class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <flux:text size="xs" class="uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $item['label'] }}</flux:text>
                                <flux:badge :color="$item['color']" size="sm" :icon="$item['icon']" inset="top bottom">{{ $item['trend'] }}</flux:badge>
                            </div>
                            <flux:heading size="xl">{{ $item['value'] }}</flux:heading>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $item['meta'] }}</flux:text>
                        </flux:card>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
