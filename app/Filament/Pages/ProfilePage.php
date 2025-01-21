<?php

namespace App\Filament\Pages;

use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilePage extends Page
{
    use HasPageShield;
    use InteractsWithActions;
    use InteractsWithFormActions;

    protected static string $view = 'filament.pages.profile-page';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'My Profile';

    protected ?string $subheading = 'update profile information';

    public ?array $profileData = [];

    public ?array $credentialData = [];

    public function mount(): void
    {
        $this->profileData = [
            ...$this->profileData,
            'email' => auth()->user()->email,
            'name' => auth()->user()->name,
            'role' => auth()->user()->getRoleNames()[0],
        ];

        /** @phpstan-ignore-next-line  */
        $this->editProfileForm->fill($this->profileData);
    }

    protected function getForms(): array
    {
        return [
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    public function editProfileForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Update profile')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->unique(ignorable: auth()->user())
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->unique(ignorable: auth()->user())
                        ->required(),
                    Forms\Components\TextInput::make('role')
                        ->disabled()
                        ->maxLength(255),
                ]),
        ])->model(User::class)->statePath('profileData');
    }

    public function updateProfile(): void
    {
        /** @phpstan-ignore-next-line  */
        $this->editProfileForm->getState();

        auth()->user()->forceFill([
            'name' => $this->profileData['name'],
            'email' => $this->profileData['email'],
        ])->save();

        Notification::make()
            ->title('Profile updated')
            ->success()
            ->body('Your profile has been updated successfully.<br/>Refresh the page to take effect the changes.')
            ->duration(6000)
            ->actions([
                \Filament\Notifications\Actions\Action::make('refresh')
                    ->label('Refresh Page')
                    ->url(ProfilePage::getUrl())
                    ->button()
                    ->color('success')
                    ->close(),
            ])
            ->send();

        /** @phpstan-ignore-next-line  */
        $this->editProfileForm->fill($this->profileData);

    }

    public function editPasswordForm(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Update Password')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('current_password')
                        ->currentPassword()
                        ->password()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('New Password')
                        ->password()
                        ->confirmed()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required()
                        ->maxLength(255),
                ]),
        ])->statePath('credentialData');
    }

    public function updatePassword(): void
    {
        /** @phpstan-ignore-next-line  */
        $this->editPasswordForm->getState();
        auth()->user()->forceFill([
            'password' => Hash::make($this->credentialData['password']),
        ])->save();

        Notification::make()
            ->title('Password updated')
            ->success()
            ->body('Your password has been updated successfully.')
            ->send();

        if (session() !== null) {
            session()->put([
                'password_hash_'.Auth::getDefaultDriver() => Auth::user()?->getAuthPassword(),
            ]);
        }

        /** @phpstan-ignore-next-line  */
        $this->editPasswordForm->fill([]);

    }
}
