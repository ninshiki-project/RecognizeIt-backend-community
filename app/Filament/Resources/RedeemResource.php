<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedeemResource\Pages;
use App\Http\Controllers\Api\Enum\RedeemStatusEnum;
use App\Models\Redeem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRedeems::route('/'),
        ];
    }
}
