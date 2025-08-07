<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $nama
 * @property string $alamat
 * @property string $kontak
 * @property string $username_pppoe
 * @property string $password_pppoe
 * @property int $paket_id
 * @property string|null $ip_pool
 * @property string $status
 * @property string|null $foto_ktp
 * @property \Illuminate\Support\Carbon $tanggal_daftar
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PaketInternet $paket
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tagihan> $tagihan
 * @property-read int|null $tagihan_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereKontak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUsernamePppoe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePasswordPppoe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePaketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereIpPool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereFotoKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereTanggalDaftar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer aktif()
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'alamat',
        'kontak',
        'username_pppoe',
        'password_pppoe',
        'paket_id',
        'ip_pool',
        'status',
        'foto_ktp',
        'tanggal_daftar',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the package associated with the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketInternet::class, 'paket_id');
    }

    /**
     * Get all bills for this customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Generate unique PPPoE username.
     *
     * @param string $baseName
     * @return string
     */
    public static function generateUsername(string $baseName): string
    {
        $username = strtolower(str_replace(' ', '', $baseName));
        $counter = 1;
        $originalUsername = $username;

        while (static::where('username_pppoe', $username)->exists()) {
            $username = $originalUsername . sprintf('%03d', $counter);
            $counter++;
        }

        return $username;
    }

    /**
     * Generate secure PPPoE password.
     *
     * @param int $length
     * @return string
     */
    public static function generatePassword(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
}