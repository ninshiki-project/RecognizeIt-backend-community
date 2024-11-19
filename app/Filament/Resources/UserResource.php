<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Models\Designations;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->disabledOn('edit')
                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'This will be updated with the information once the user login.')
                    ->hintColor(Color::Orange)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->disabledOn('edit')
                    ->email()
                    ->required(),
                Forms\Components\Select::make('roles')
                    ->required()
                    ->live()
                    ->reactive()
                    ->native(false)
                    ->visibleOn('create')
                    ->preload()
                    ->relationship('roles', 'name'),
                Forms\Components\TextInput::make('password')
                    ->reactive()
                    ->visibleOn('create')
                    ->hidden(fn (Forms\Get $get): bool => ! $get('roles') || $get('roles') === 'Member')
                    ->revealable()
                    ->required(fn (Forms\Get $get): bool => ! $get('roles') || $get('roles') === 'Administrator')
                    ->password(),
                Forms\Components\Select::make('department')
                    ->required()
                    ->native(false)
                    ->relationship('departments', 'name'),
                Forms\Components\Select::make('designation')
                    ->required()
                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'To add designation, update the Designations model file.')
                    ->hintColor(Color::Orange)
                    ->native(false)
                    ->options(Designations::all()->pluck('name', 'name')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderBy('created_at', 'desc'))
            ->columns(components: [
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->tooltip(function (User $user): string {
                        return match ($user->status) {
                            UserEnum::Invited => 'User is Invited',
                            UserEnum::Active => 'User is Active',
                            UserEnum::Deactivate => 'User has been Deactivated by the administrator',
                            default => '',
                        };
                    }),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (User $user): bool => $user->id === auth()->id()),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('update_status')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Change User Account Status')
                                ->required()
                                ->default(function (User $record) {
                                    return $record->status;
                                })
                                ->native(false)
                                ->preload()
                                ->disableOptionWhen(function (string $value, User $user): bool {
                                    if ($user->status !== UserEnum::Invited) {
                                        return $value === UserEnum::Invited->value;
                                    }
                                    if ($user->status === UserEnum::Invited) {
                                        return $value === UserEnum::Active->value;
                                    }
                                })
                                ->options(UserEnum::class),
                        ])
                        ->requiresConfirmation()
                        ->action(function (User $user, array $data) {
                            $user->update([
                                'status' => $data['status'],
                            ]);
                        })
                        ->modalWidth(MaxWidth::Small)
                        ->modalAlignment(Alignment::Center)
                        ->icon('heroicon-o-user-circle')
                        ->label('Update Status'),
                    Tables\Actions\Action::make('update_role')
                        ->form([
                            Forms\Components\Select::make('roles')
                                ->label('Change User Role to:')
                                ->required()
                                ->live()
                                ->reactive()
                                ->native(false)
                                ->preload()
                                ->default(function (User $record) {
                                    return $record->getRoleNames()[0] ?? null;
                                })
                                ->options(Role::all()->pluck('name', 'name')),
                            Forms\Components\TextInput::make('password')
                                ->label('Set Temporary Password:')
                                ->reactive()
                                ->hidden(fn (Forms\Get $get): bool => ! $get('roles') || $get('roles') === 'Member')
                                ->revealable()
                                ->required(fn (Forms\Get $get): bool => ! $get('roles') || $get('roles') === 'Administrator')
                                ->password(),
                        ])
                        ->requiresConfirmation()
                        ->action(function (User $user, array $data) {
                            $user->update([
                                'password' => $data['password'] ?? null,
                            ]);
                            $user->syncRoles($data['roles']);
                        })
                        ->modalWidth(MaxWidth::Small)
                        ->modalAlignment(Alignment::Center)
                        ->icon('heroicon-o-shield-check')
                        ->label('Update Role'),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->hidden(fn (User $user): bool => $user->id === auth()->id())
                    ->icon('heroicon-o-ellipsis-horizontal-circle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
