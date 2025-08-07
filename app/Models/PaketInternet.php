<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PaketInternet
 *
 * @property int $id
 * @property string $nama_paket
 * @property float $harga
 * @property string $bandwidth
 * @property string|null $deskripsi
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereNamaPaket($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereBandwidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaketInternet aktif()
 * @method static \Database\Factories\PaketInternetFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class PaketInternet extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paket_internet';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_paket',
        'harga',
        'bandwidth',
        'deskripsi',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all customers using this package.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'paket_id');
    }

    /**
     * Scope a query to only include active packages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get formatted price.
     *
     * @return string
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}