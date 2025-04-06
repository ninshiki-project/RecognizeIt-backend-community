<?php

namespace App\Filament\Resources;

use App\Enum\RedeemDeclineReasonEnum;
use App\Enum\RedeemStatusEnum;
use App\Filament\Actions\RedeemDeclined;
use App\Filament\Resources\RedeemResource\Pages;
use App\Filament\Resources\RedeemResource\Widgets\RedeemStatOverview;
use App\Models\Redeem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RedeemResource extends Resource
{
    protected static ?string $model = Redeem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Store';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Redeems';

    protected static ?string $label = '';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('shop_id')
                    ->relationship('shop', 'id')
                    ->required(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(RedeemStatusEnum::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->emptyStateHeading('No Data Available')
            ->emptyStateDescription('Once an employee redeem a product from the shop, it will appear here.')
            ->actionsPosition(Tables\Enums\ActionsPosition::BeforeColumns)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Transaction ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('shop.id')
                    ->label('Shop ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee Name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.departments.name')
                    ->label('Employee Department')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Redeem Product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Redeem Product Price')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Redeem Date')
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Approve')
                    ->visible(fn (Redeem $redeem) => $redeem->status === RedeemStatusEnum::WAITING_APPROVAL)
                    ->label('Approve')
                    ->hiddenLabel()
                    ->size(ActionSize::Medium)
                    ->icon(RedeemStatusEnum::APPROVED->getIcon())
                    ->tooltip('Approve Redeem')
                    ->requiresConfirmation()
                    ->action(function (Redeem $redeem) {
                        $redeem->status = RedeemStatusEnum::APPROVED;
                        $redeem->product->stock--;
                        $redeem->save();
                    })
                    ->modalIcon(RedeemStatusEnum::APPROVED->getIcon()),
                Tables\Actions\Action::make('Processing')
                    ->visible(fn (Redeem $redeem) => $redeem->status === RedeemStatusEnum::APPROVED)
                    ->label('Processing')
                    ->color('warning')
                    ->hiddenLabel()
                    ->size(ActionSize::Medium)
                    ->icon(RedeemStatusEnum::PROCESSING->getIcon())
                    ->tooltip('Process Redeem')
                    ->requiresConfirmation()
                    ->action(function (Redeem $redeem) {
                        $redeem->status = RedeemStatusEnum::PROCESSING;
                        $redeem->save();
                    })
                    ->modalIcon(RedeemStatusEnum::PROCESSING->getIcon()),
                Tables\Actions\Action::make('Redeemed')
                    ->visible(fn (Redeem $redeem) => $redeem->status === RedeemStatusEnum::APPROVED || $redeem->status === RedeemStatusEnum::PROCESSING)
                    ->color('secondary')
                    ->label('Redeemed')
                    ->hiddenLabel()
                    ->size(ActionSize::Medium)
                    ->icon(RedeemStatusEnum::REDEEMED->getIcon())
                    ->tooltip('Redeemed')
                    ->requiresConfirmation()
                    ->action(function (Redeem $redeem) {
                        $redeem->status = RedeemStatusEnum::REDEEMED;
                        $redeem->save();
                    })
                    ->modalIcon(RedeemStatusEnum::REDEEMED->getIcon()),
                Tables\Actions\Action::make('decline')
                    ->visible(fn (Redeem $redeem) => $redeem->status === RedeemStatusEnum::WAITING_APPROVAL)
                    ->label('Decline')
                    ->hiddenLabel()
                    ->color('danger')
                    ->size(ActionSize::Medium)
                    ->icon(RedeemStatusEnum::DECLINED->getIcon())
                    ->tooltip('Decline Redeem')
                    ->requiresConfirmation()
                    ->modalIcon(RedeemStatusEnum::DECLINED->getIcon())
                    ->form([
                        Forms\Components\Select::make('category')
                            ->required()
                            ->native(false)
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (is_null($state)) {
                                    $set('description', '');
                                }
                                switch ($state) {
                                    case RedeemDeclineReasonEnum::ITEM_NO_LONGER_AVAILABLE->getLabel():
                                        $set('description', RedeemDeclineReasonEnum::ITEM_NO_LONGER_AVAILABLE->getDescription());
                                        break;
                                    case RedeemDeclineReasonEnum::INCORRECT_ITEM_PRICING->getLabel():
                                        $set('description', RedeemDeclineReasonEnum::INCORRECT_ITEM_PRICING->getDescription());
                                        break;
                                    case RedeemDeclineReasonEnum::EXCEED_REDEMPTION_LIMIT->getLabel():
                                        $set('description', RedeemDeclineReasonEnum::EXCEED_REDEMPTION_LIMIT->getDescription());
                                        break;
                                    case RedeemDeclineReasonEnum::ITEM_TEMPORARILY_UNAVAILABLE->getLabel():
                                        $set('description', RedeemDeclineReasonEnum::ITEM_TEMPORARILY_UNAVAILABLE->getDescription());
                                        break;
                                }
                            })
                            ->options(RedeemDeclineReasonEnum::class),
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->required(),
                    ])
                    ->action(fn (array $data, Redeem $record, RedeemDeclined $redeemDeclined) => $redeemDeclined->handle($record, $data)),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            RedeemStatOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRedeems::route('/'),
        ];
    }
}
