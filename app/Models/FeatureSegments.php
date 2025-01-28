<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;

/**
 * 
 *
 * @property int $id
 * @property string $feature
 * @property string $scope
 * @property array<array-key, mixed> $values
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $description
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read mixed $title
 * @method static \Database\Factories\FeatureSegmentsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeatureSegments whereValues($value)
 * @mixin \Eloquent
 */
class FeatureSegments extends Model
{
    use HasFactory;

    protected $appends = [
        'title',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature',
        'scope',
        'values',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'values' => 'array',
            'active' => 'boolean',
        ];
    }

    public function resolve(mixed $scope): bool
    {
        $meetsSegmentCriteria = in_array($scope->{$this->scope}, (array) $this->values, true);

        /*
         * This check is TRUE if the segment is activated and the scope meets the criteria. Additionally,
         * it returns TRUE if the segment is deactivated and doesn't meet the criteria.
         */
        if (($this->active && $meetsSegmentCriteria) || (! $this->active && ! $meetsSegmentCriteria)) {
            return true;
        }

        /*
         * This check is FALSE if the segment is activated and the scope doesn't meet the criteria.
         * Additionally, it returns FALSE if the segment is deactivated and meet's the criteria.
         */
        return false;

    }

    public function title(): Attribute
    {
        return Attribute::make(
            get: fn () => class_exists($this->feature) ? $this->feature::title() : '(Feature Deleted)',
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf(
                '%s %s for customers who have any of these %s — %s.',
                $this->title,
                $this->active ? 'activated' : 'deactivated',
                str($this->scope)->plural(),
                implode(', ', (array) $this->values)),
        );
    }

    public static function allFeatures(): array
    {
        return collect(Feature::all())
            ->map(fn ($value, $key) => [
                'id' => $key,
                'name' => $name = str(class_basename($key))->snake()->replace('_', ' ')->title()->toString(),
                'state' => $value,
                'description' => "This feature covers $name on the mobile app.",
            ])
            ->values()
            ->toArray();
    }

    public static function featureOptionsList(): array
    {
        return collect(self::allFeatures())->pluck('name', 'id')->toArray();
    }

    public static function segmentOptionsList(): array
    {
        return collect((array) config('pennant.segments'))
            ->pluck('column')
            ->mapWithKeys(fn ($segment) => [$segment => str($segment)->plural()->title()->toString()])
            ->toArray();
    }
}
