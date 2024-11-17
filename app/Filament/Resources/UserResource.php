<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Models\Designations;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'This will be updated with the information once the user login.')
                    ->hintColor(Color::Orange)
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Select::make('roles')
                    ->required()
                    ->live()
                    ->reactive()
                    ->native(false)
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
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('update_status')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('User Account Status')
                                ->required()
                                ->native(false)
                                ->preload()
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
                        ->color(COlor::Orange)
                        ->label('Update Status'),
                    Tables\Actions\DeleteAction::make(),
                ])
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
