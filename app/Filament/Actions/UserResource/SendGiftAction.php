<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: UpdateStatusAction.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Filament\Actions\UserResource;

use App\Enum\GiftEnum;
use App\Enum\WalletsEnum;
use App\Http\Resources\UserPostedByResource;
use App\Models\Application;
use App\Models\Gift;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class SendGiftAction
{
    /**
     * @param  \Filament\Tables\Actions\Action|Action  $action
     * @return array
     */
    public function handle(Action|\Filament\Tables\Actions\Action $action): array
    {
        return [
            $action->form([
                Forms\Components\Select::make('type')
                    ->label('Gift Type')
                    ->native(false)
                    ->required()
                    ->reactive()
                    ->disableOptionWhen(fn (string $value): bool => $value === GiftEnum::SHOP->value)
                    ->options(GiftEnum::class),
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->visible(fn (Forms\Get $get): bool => filled($get('type')) && $get('type') === GiftEnum::COINS->value)
                    ->required(fn (Forms\Get $get): bool => filled($get('type')) && $get('type') === GiftEnum::COINS->value),
                Forms\Components\Textarea::make('message')
                    ->label('Please provide a message on why you are sending a gift to this employee.')
                    ->minLength(25)
                    ->rows(5)
                    ->required(),
            ])
                ->action(function (User $user, array $data): void {
                    $giftFeature = Application::first()->more_configs['gift'];
                    $exchangeRate = $giftFeature['exchange_rate'] ?? 1;
                    $convertedAmount = $data['amount'] * $exchangeRate;

                    $giftRecord = Gift::create([
                        'type' => $data['type'],
                        'amount' => $data['amount'],
                        'to' => $user->id,
                        'by' => auth()->user()->id,
                        'gift' => [
                            'fromOrg' => true, // this means that the gift was on behalf of the org/company
                            'type' => $data['type'],
                            'exchange_rate' => $exchangeRate,
                            'converted_amount' => $convertedAmount,
                            'amount' => $data['amount'],
                            'users' => [
                                'sender' => null,
                                'receiver' => UserPostedByResource::make($user),
                            ],
                        ],
                    ]);

                    $recipientWallet = $user->getWallet(WalletsEnum::DEFAULT->value);
                    $recipientWallet->deposit($convertedAmount, [
                        'title' => 'Ninshiki Wallet',
                        'model' => [
                            'model' => Gift::class,
                            'record' => $giftRecord,
                        ],
                        'fromOrg' => true,
                        'description' => 'The Organization sent you a coins as a gift.',
                        'message' => $data['message'],
                        'date_at' => Carbon::now(),
                    ]);

                    Notification::make('gift')
                        ->title('Gift sent')
                        ->body('The Gift has been received by the employee.')
                        ->success()
                        ->send();

                })
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-gift')
                ->modalIconColor(Color::Green)
                ->modalDescription(new HtmlString('Are you sure you would like to do this?<br/><i class="text-red-400">This action is not reversible.</i>'))
                ->modalWidth(MaxWidth::Small)
                ->modalAlignment(Alignment::Center)
                ->modalFooterActionsAlignment(Alignment::Right)
                ->outlined()
                ->color(Color::Zinc)
                ->label('Send Gift')
                ->icon('heroicon-o-gift'),
        ];
    }
}
