<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput as FormTextInput;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Session;

class UserList extends Component implements HasTable, HasForms, HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'user',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => '🛡️ Admin',
                        'user' => '👤 User',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Joined'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->recordUrl(null)
            ->actions([
                ActionGroup::make([
                    Action::make('loginAs')
                        ->label('Login as')
                        ->icon(Heroicon::ArrowRightStartOnRectangle)
                        ->color('info')
                        ->action(fn (User $record) => $this->loginAs($record->id)),
                    Action::make('edit')
                        ->label('Edit')
                        ->icon(Heroicon::PencilSquare)
                        ->color('gray')
                        ->form([
                            FormTextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            FormTextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            FormSelect::make('role')
                                ->options([
                                    'user' => '👤 User',
                                    'admin' => '🛡️ Admin',
                                ])
                                ->required(),
                        ])
                        ->fillForm(fn (User $record): array => [
                            'name' => $record->name,
                            'email' => $record->email,
                            'role' => $record->role,
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->update([
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'role' => $data['role'],
                            ]);
                        })
                        ->modalHeading('Edit User')
                        ->modalWidth(Width::Small)
                        ->modalIcon(Heroicon::PencilSquare),
                    Action::make('delete')
                        ->label('Delete')
                        ->icon(Heroicon::Trash)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (User $record) => $this->deleteUser($record->id)),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->size('sm')
                    ->button(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'user' => '👤 User',
                        'admin' => '🛡️ Admin',
                    ])
                    ->label('Filter by Role'),
                Filter::make('created_at')
                    ->label('Join Date Range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = \Filament\Tables\Filters\Indicator::make('Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add User')
                    ->icon('heroicon-m-plus')
                    ->form($this->getFormSchema())
                    ->action(function (array $data) {
                        User::create([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'password' => Hash::make($data['password']),
                            'role' => $data['role'],
                        ]);
                    })
                    ->modalHeading('Create New User'),
            ]);
    }

    public function deleteUser($userId): void
    {
        User::find($userId)?->delete();
    }

    public function loginAs($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            // Store the original admin user ID in session
            Session::put('original_admin_id', Auth::id());
            Session::put('impersonating', true);
            
            Auth::login($user);
            $this->redirect(route('dashboard'));
        }
    }

    public function switchBackToAdmin(): void
    {
        $originalAdminId = Session::get('original_admin_id');
        if ($originalAdminId) {
            $admin = User::find($originalAdminId);
            if ($admin) {
                Auth::login($admin);
                Session::forget(['original_admin_id', 'impersonating']);
                $this->redirect(route('dashboard'));
            }
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(User::class, 'email', ignoreRecord: true),
            TextInput::make('password')
                ->password()
                ->revealable()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->minLength(8),
            Select::make('role')
                ->options([
                    'user' => '👤 User',
                    'admin' => '🛡️ Admin',
                ])
                ->default('user')
                ->required(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.user-list');
    }
}
