<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: FeatureSegmentsResource.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureSegmentsResource\ManageFeatureSegments;
use App\Models\FeatureSegments;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class FeatureSegmentsResource extends Resource
{
    protected static ?string $model = FeatureSegments::class;

    protected static ?string $navigationParentItem = 'Settings';

    protected static ?string $modelLabel = 'Manage Features & Segments';

    public static function getNavigationSort(): ?int
    {
        return PostingLimitResource::getNavigationSort() - 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('feature')
                    ->native(false)
                    ->preload()
                    ->required()
                    ->options(FeatureSegments::featureOptionsList())
                    ->noSearchResultsMessage('No Features Found')
                    ->columnSpanFull(),

                Select::make('scope')
                    ->live()
                    ->preload()
                    ->native(false)
                    ->afterStateUpdated(fn (Set $set) => $set('values', null))
                    ->required()
                    ->columnSpanFull()
                    ->noSearchResultsMessage('No Scope Found')
                    ->options(FeatureSegments::segmentOptionsList()),

                ...static::createValuesFields(),

                Select::make('active')
                    ->native(false)
                    ->preload()
                    ->label('Status')
                    ->options([true => 'Activate', false => 'Deactivate'])
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                            ->where('feature', $get('feature'))
                            ->where('scope', $get('scope'))
                            ->where('active', $get('active'))
                    )
                    ->validationMessages([
                        'unique' => 'Feature segmentation already exists! Please note that each feature scope can only have an activated and a deactivated segment. Modify existing segment or remove it and create a new segment.',
                    ])
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable(['feature'])
                    ->searchable(['feature']),
                Tables\Columns\TextColumn::make('values')
                    ->label('Segment')
                    ->wrap()
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'ACTIVATED' ? 'success' : 'danger')
                    ->weight(FontWeight::ExtraBold)
                    ->getStateUsing(function (FeatureSegments $record) {
                        return $record->active ? 'ACTIVATED' : 'DEACTIVATED';
                    }),
            ])
            ->defaultSort('feature')
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(FeatureSegments::featureOptionsList()),
                Tables\Filters\SelectFilter::make('scope')
                    ->options(FeatureSegments::segmentOptionsList()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Modify')
                    ->modalHeading('Modify Feature Segment'),

                Tables\Actions\DeleteAction::make()
                    ->modalHeading('Removing this feature segment cannot be undone!')
                    ->modalDescription(fn (FeatureSegments $record) => $record->description)
                    ->label('Remove'),
            ]);
    }

    protected static function createValuesFields(): array
    {
        /** @phpstan-ignore-next-line */
        return collect(config('pennant.segments'))
            ->map(
                function ($segment) {
                    $column = $segment['column'];
                    $model = $segment['source']['model'];
                    $value = $segment['source']['value'];
                    $key = $segment['source']['key'];

                    return Select::make('values')
                        ->label(str($column)->plural()->title())
                        ->hidden(fn (Get $get) => $get('scope') !== $column)
                        ->required()
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull()
                        ->getSearchResultsUsing(
                            fn (string $search): array => $model::where($value, 'like', "%{$search}%")
                                ->limit(50)->pluck($value, $key)->toArray()
                        );
                }
            )
            ->toArray();
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFeatureSegments::route('/'),
        ];
    }
}
