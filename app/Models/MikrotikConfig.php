<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MikrotikConfig
 *
 * @property int $id
 * @property string $name
 * @property string $host
 * @property int $port
 * @property string $username
 * @property string $password
 * @property bool $is_active
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $last_sync
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereLastSync($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MikrotikConfig active()
 * @method static \Database\Factories\MikrotikConfigFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class MikrotikConfig extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'is_active',
        'description',
        'last_sync',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_sync' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Scope a query to only include active configurations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}