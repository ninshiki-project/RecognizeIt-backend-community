<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostingLimitResource\Pages;
use App\Models\PostingLimit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostingLimitResource extends Resource
{
    protected static ?string $model = PostingLimit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('designations_id')
                    ->unique(ignoreRecord: true)
                    ->label('Designation')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->relationship('designations', 'name')
                    ->required(),
                Forms\Components\TextInput::make('limit')
                    ->label('Limit')
                    ->hint('Allow credit limit for posting')
                    ->rules([
                        'numeric',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('designations.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('limit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManagePostingLimits::route('/'),
        ];
    }
}
