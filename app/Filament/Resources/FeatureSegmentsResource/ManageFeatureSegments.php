<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: ManageFeatureSegments.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Resources\FeatureSegmentsResource;

use App\Filament\Resources\FeatureSegmentsResource;
use App\Models\FeatureSegments;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Colors\Color;
use Laravel\Pennant\Feature;

class ManageFeatureSegments extends ManageRecords
{
    protected static string $resource = FeatureSegmentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md')
                ->color(Color::Blue)
                ->modalHeading(__('Create Feature Segment'))
                ->label(__('Segment Feature'))
                ->after(fn (FeatureSegments $record) => $this->afterCreate($record)),

            Actions\Action::make('activate_for_all')
                ->label(__('Activate'))
                ->color(Color::Green)
                ->modalWidth('md')
                ->modalDescription(fn ($record) => __('This action will activate the selected feature for users.'))
                ->form([
                    Forms\Components\Select::make('feature')
                        ->native(false)
                        ->preload()
                        ->label(__('Feature'))
                        ->required()
                        ->options(FeatureSegments::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Activate'))
                ->action(fn ($data) => $this->activateForAll($data['feature'])),

            Actions\Action::make('deactivate_for_all')
                ->label(__('Deactivate for All'))
                ->color(Color::Orange)
                ->modalWidth('md')
                ->label(__('Deactivate'))
                ->modalDescription(fn ($record) => __('This action will deactivate this feature for users.'))
                ->form([
                    Forms\Components\Select::make('feature')
                        ->native(false)
                        ->preload()
                        ->label(__('Feature'))
                        ->required()
                        ->options(FeatureSegments::featureOptionsList())
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Deactivate'))
                ->action(fn ($data) => $this->deactivateForAll($data['feature'])),

            Actions\Action::make('purge_features')
                ->color(Color::Red)
                ->modalWidth('md')
                ->label(__('Purge'))
                ->modalDescription(fn ($record) => __('This action will purge resolved features from storage.'))
                ->form([
                    Forms\Components\Select::make('feature')
                        ->native(false)
                        ->preload()
                        ->label(__('Feature'))
                        ->selectablePlaceholder(false)
                        ->options(array_merge([null => __('All Features')], FeatureSegments::featureOptionsList()))
                        ->noSearchResultsMessage('No Features Found')
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Purge'))
                ->action(fn ($data) => $this->purgeFeatures($data['feature'])),
        ];
    }

    public function activateForAll(string $feature): void
    {
        Feature::activateForEveryone($feature);

        Notification::make()->success()->title(__('Done!'))
            ->body(__("{$feature::title()} activated for users."))->send();
    }

    private function deactivateForAll(string $feature): void
    {
        Feature::deactivateForEveryone($feature);

        Notification::make()->success()->title(__('Done!'))
            ->body(__("{$feature::title()} deactivated for users."))
            ->send();
    }

    private function purgeFeatures(?string $feature): void
    {
        Feature::purge($feature);

        $featureTitle = is_null($feature)
            ? __('All features')
            : $feature::title().__(' feature');

        Notification::make()->success()->title(__('Done!'))
            ->body(__("$featureTitle successfully purged from storage."))
            ->send();
    }

    public function afterCreate(FeatureSegments $featureSegment): void
    {
        Feature::purge($featureSegment->feature);
    }
}
