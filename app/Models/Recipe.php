<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @mixin IdeHelperRecipe
 */
class Recipe extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia, Searchable, SoftDeletes;

    protected $fillable = [
        'name',
        'serving',
        'ingredients',
        'instructions',
        'description',
        'cook_time',
        'prep_time',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'published_at' => 'timestamp',
        'user_id' => 'integer',
    ];

    protected $hidden = ['id'];

    protected $appends = ['hero', 'hero_preview'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected function hero(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMedia('hero')?->getUrl() ?? '',
        );
    }

    protected function heroPreview(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMedia('hero')->preview_url ?? '',
        );
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        /** @phpstan-ignore-next-line  */
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 600, 400)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('hero')
            ->singleFile();
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            // 'created_at' => $this->created_at->timestamp,
            // 'ingredients' => $this->ingredients,
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->orderBy((new Category)->determineOrderColumnName());
    }

    /**
     * @codeCoverageIgnore
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class)->orderBy((new Label)->determineOrderColumnName());
    }
}
