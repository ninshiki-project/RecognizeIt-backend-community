<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\FeatureSushi;
use App\Models\FeaturesWrapper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Laravel\Pennant\Feature;

class FeatureRelationManager extends RelationManager
{
    protected static string $relationship = 'featureSushi';

    protected static ?string $title = 'Features';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('feature')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(FeatureSushi::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('state')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
