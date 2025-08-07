<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Tagihan
 *
 * @property int $id
 * @property int $customer_id
 * @property string $periode
 * @property float $jumlah
 * @property \Illuminate\Support\Carbon $jatuh_tempo
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pembayaran> $pembayaran
 * @property-read int|null $pembayaran_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan wherePeriode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereJatuhTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan belumLunas()
 * @method static \Illuminate\Database\Eloquent\Builder|Tagihan jatuhTempo()
 * @method static \Database\Factories\TagihanFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Tagihan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tagihan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'periode',
        'jumlah',
        'jatuh_tempo',
        'status',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jumlah' => 'decimal:2',
        'jatuh_tempo' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the bill.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all payments for this bill.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }

    /**
     * Scope a query to only include unpaid bills.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status', 'belum_lunas');
    }

    /**
     * Scope a query to only include overdue bills.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJatuhTempo($query)
    {
        return $query->where('jatuh_tempo', '<', now())
                    ->where('status', 'belum_lunas');
    }

    /**
     * Get formatted amount.
     *
     * @return string
     */
    public function getFormattedJumlahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Check if bill is overdue.
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->jatuh_tempo->isPast() && $this->status === 'belum_lunas';
    }
}