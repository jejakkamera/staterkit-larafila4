<?php

namespace App\Livewire\Admin;

use App\Models\WebsiteSetting;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\Redirect;

class WebsiteSettings extends Component implements HasForms
{
    use InteractsWithForms;

    public ?WebsiteSetting $record = null;
    public ?array $data = [];

    public function mount(): void
    {
        $this->record = WebsiteSetting::first() ?? WebsiteSetting::create();
        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('website_name')
                    ->label('Website Name')
                    ->placeholder('e.g., Laravel Starter Kit')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('website-settings')
                    ->visibility('public')
                    ->imagePreviewHeight('100')
                    ->downloadable(),

                FileUpload::make('favicon')
                    ->label('Favicon')
                    ->disk('public')
                    ->directory('website-settings')
                    ->visibility('public')
                    ->imagePreviewHeight('50')
                    ->downloadable(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $validated = validator($data, [
            'website_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'favicon' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $this->record->update($validated);

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();

        $this->redirect('/admin/settings');
    }

    public function render(): View
    {
        return view('livewire.admin.website-settings');
    }
}
