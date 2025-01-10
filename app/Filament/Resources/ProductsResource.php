<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductsResource\Pages;
use App\Models\Products;
use App\Models\Scopes\ProductAvailableScope;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Filament\Forms;
use Filament\Forms\Components\BaseFileUpload;
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
use League\Flysystem\UnableToCheckFileExistence;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductsResource extends Resource
{
    protected static ?string $model = Products::class;

    protected static ?string $navigationParentItem = 'Store';

    protected static ?int $navigationSort = 2;

    public static ?string $cloudinaryPublicId = null;

    public static ?string $oldCloudinaryPublicId = null;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('cloudinary')
                    ->directory('products')
                    ->visibility('private')
                    ->maxSize(10240) // 10MB
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
                    ->saveUploadedFileUsing((static function (BaseFileUpload $component, TemporaryUploadedFile $file, Forms\Set $set): ?string {
                        try {
                            if (! $file->exists()) {
                                return null;
                            }
                        } catch (UnableToCheckFileExistence $exception) {
                            return null;
                        }

                        $uploadedFile = $file->storeOnCloudinaryAs($component->getDirectory(), $component->getUploadedFileNameForStorage($file));
                        self::$cloudinaryPublicId = $uploadedFile->getPublicId();

                        return $uploadedFile->getSecurePath();
                    }))
                    ->reactive()
                    ->columnSpanFull()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withoutGlobalScope(new ProductAvailableScope)->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->formatStateUsing(fn ($state) => number_format($state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['cloudinary_id'] = self::$cloudinaryPublicId;

                        return $data;
                    })
                    ->before(function (Products $record) {
                        self::$oldCloudinaryPublicId = $record->cloudinary_id;
                    })
                    ->after(function () {
                        // delete cloudinary id
                        Cloudinary::destroy(self::$oldCloudinaryPublicId);
                    })
                    ->modalAlignment(Alignment::Center)
                    ->modalWidth(MaxWidth::FitContent)
                    ->modalFooterActionsAlignment(Alignment::Right),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Products $record, Tables\Actions\DeleteAction $action) {
                        // prevent deleting if the record is being used in other model
                        if ($record->shop()->exists() || $record->redeems()->exists()) {
                            Notification::make('stop')
                                ->title('Unable to Delete')
                                ->body('Product has existing record in Shop or Redeem')
                                ->warning()
                                ->send();

                            return;
                        }
                        if ($record->cloudinary_id) {
                            Cloudinary::destroy($record->cloudinary_id);
                            $action->success();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn (Collection $records) => $records->each(function (Model $record) {
                            if ($record->cloudinary_id && (! $record->shop()->exists() || ! $record->redeems()->exists())) {
                                Cloudinary::destroy($record->cloudinary_id);
                            }
                        })),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
