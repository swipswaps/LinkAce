<?php

namespace App\Models;

use App\Scopes\OrderNameScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * @package App\Models
 * @property int                        $id
 * @property int                        $user_id
 * @property string                     $name
 * @property string|null                $description
 * @property int|null                   $parent_category
 * @property int                        $is_private
 * @property \Carbon\Carbon|null        $created_at
 * @property \Carbon\Carbon|null        $updated_at
 * @property string|null                $deleted_at
 * @property-read Collection|Category[] $childCategories
 * @property-read Collection|Link[]     $links
 * @property-read User                  $user
 * @method static Builder|Category parentOnly()
 * @method static Builder|Category byUser($user_id)
 */
class Category extends RememberedModel
{
    use SoftDeletes;

    public $table = 'categories';

    public $fillable = [
        'user_id',
        'name',
        'description',
        'parent_category',
        'is_private',
    ];

    public $rememberCacheTag = 'category_queries';

    /**
     * Add the OrderNameScope to the Tag model
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new OrderNameScope());
    }

    /*
     | ========================================================================
     | SCOPES
     */

    /**
     * Scope for the user relation
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param int                                    $user_id
     * @return mixed
     */
    public function scopeByUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_category');
    }

    /*
     | ========================================================================
     | RELATIONSHIPS
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(Link::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childCategories()
    {
        return $this->hasMany(Category::class, 'parent_category');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category');
    }
}