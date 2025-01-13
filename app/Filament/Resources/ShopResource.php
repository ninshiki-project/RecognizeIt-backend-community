<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopResource\Pages;
use App\Filament\Resources\ShopResource\Widgets\ShopStatOverview;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationParentItem = 'Store';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                FileUpload::make('image')
                    ->deletable(false)
                    ->fetchFileInformation(false)
                    ->removeUploadedFileButtonPosition('right')
                    ->afterStateHydrated(static function (BaseFileUpload $component, string|array|null $state) {
                        if (blank($state)) {
                            $component->state([]);

                            return;
                        }
                        $component->state([((string) Str::uuid()) => $state]);
                    })
                    ->afterStateUpdated(static function (BaseFileUpload $component, $state) {
                        $component->state([(string) Str::uuid() => $state]);
                    })
                    ->getUploadedFileUsing(static function (BaseFileUpload $component, string $file): array {
                        return [
                            'name' => basename($file),
                            'size' => 0,
                            'type' => null,
                            'url' => $file,
                        ];
                    })
                    ->visibleOn('view')
                    ->image(),
                Forms\Components\Select::make('product_id')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->visibleOn('view'),
                Forms\Components\TextInput::make('stock')
                    ->visibleOn('view'),
                Forms\Components\TextInput::make('price')
                    ->visibleOn('view'),
                Forms\Components\TextInput::make('status')
                    ->visibleOn('view'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Item Image')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Item Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Price')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.stock')
                    ->label('Stock')
                    ->searchable(),
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
                Tables\Actions\ViewAction::make()
                    ->mutateRecordDataUsing(function (array $data, Shop $shop): array {
                        $data['name'] = $shop->product->name;
                        $data['stock'] = $shop->product->stock;
                        $data['price'] = $shop->product->price;
                        $data['status'] = $shop->product->status;
                        $data['image'] = $shop->product->image;

                        return $data;
                    })
                    ->modalFooterActionsAlignment(Alignment::Right)
                    ->modalWidth(MaxWidth::Small)
                    ->modalAlignment(Alignment::Center),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Shop $record) {
                        // prevent deleting if the record is being used in other model
                        if ($record->redeems()->exists()) {
                            Notification::make('stop')
                                ->title('Unable to Delete')
                                ->body('Product has existing record in Shop or Redeem')
                                ->warning()
                                ->send();

                            return;
                        }
                        $record->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn (Collection $records) => $records->each(function (Model $record) {
                            if (! $record->redeems()->exists()) {
                                $record->delete();
                            }
                        })),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ShopStatOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShops::route('/'),
        ];
    }
}
