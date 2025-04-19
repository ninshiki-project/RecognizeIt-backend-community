<?php

namespace App\Filament\Resources;

use App\Enum\UserEnum;
use App\Filament\Actions\UserResource\PasswordResetRequestAction;
use App\Filament\Actions\UserResource\ResendInvitationAction;
use App\Filament\Actions\UserResource\UpdateRoleAction;
use App\Filament\Actions\UserResource\UpdateStatusAction;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\GiftsRelationManager;
use App\Filament\Resources\UserResource\Widgets\UserStatsOverview;
use App\Models\Designations;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Tapp\FilamentAuthenticationLog\RelationManagers\AuthenticationLogsRelationManager;

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
                    ->live(onBlur: true)
                    ->native(false)
                    ->visibleOn('create')
                    ->relationship('roles', 'name'),
                Forms\Components\TextInput::make('password')
                    ->hintIcon('heroicon-o-exclamation-circle', tooltip: 'If password field is leave blank, then the system will generate a random password.')
                    ->visibleOn('create')
                    ->hidden(function (Forms\Get $get, $operation): bool {
                        if (! is_null($get('roles')) && $operation === 'create') {
                            $role = Role::findById($get('roles'), 'web');
                            /** @var $role Role */
                            if ($role->hasPermissionTo('access panel', 'web')) {
                                return false;
                            }
                        }

                        return true;
                    })
                    ->reactive()
                    ->revealable()
                    ->nullable()
                    ->password(),
                Forms\Components\Select::make('department')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->live()
                    ->afterStateUpdated(function (?string $state, ?string $old, Forms\Set $set) {
                        $set('designation', null);
                    })
                    ->relationship('departments', 'name'),
                Forms\Components\Select::make('designation')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'To add designation, update the Designations model file.')
                    ->hintColor(Color::Orange)
                    ->native(false)
                    ->options(function (Forms\Get $get): array {
                        if (! $get('department')) {
                            return [];
                        }

                        return Designations::where('departments_id', $get('department'))->pluck('name', 'id')->toArray();

                    }),
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
                            UserEnum::Invited => 'Invited',
                            UserEnum::Active => 'Active',
                            UserEnum::Deactivate => 'User has been Deactivated by the administrator',
                            default => '',
                        };
                    }),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->copyable()
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departments.name')
                    ->label('Department')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designations.name')
                    ->label('Designation')
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
                Tables\Actions\ViewAction::make()
                    ->color(Color::Orange),
                Tables\Actions\EditAction::make()
                    ->modalFooterActionsAlignment(Alignment::Right)
                    ->hidden(fn (User $user): bool => $user->id === auth()->id()),
                Tables\Actions\ActionGroup::make([
                    (new UpdateStatusAction)->handle(Tables\Actions\Action::make('update_status')),
                    (new ResendInvitationAction)->handle(Tables\Actions\Action::make('resend_invitation')),
                    (new PasswordResetRequestAction)->handle(Tables\Actions\Action::make('resend_invitation')),
                    (new UpdateRoleAction)->handle(Tables\Actions\Action::make('update_role')),
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

    public static function getWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Gifts', [
                GiftsRelationManager::class,
            ]),
            RelationGroup::make('Access Logs', [
                AuthenticationLogsRelationManager::class,
            ]),

        ];
    }
}
