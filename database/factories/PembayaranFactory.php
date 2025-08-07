<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Pembayaran>
     */
    protected $model = Pembayaran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tagihan = Tagihan::inRandomOrder()->first() ?? Tagihan::factory()->create();
        
        return [
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => $this->faker->dateTimeBetween($tagihan->created_at, 'now'),
            'jumlah' => $tagihan->jumlah,
            'metode' => $this->faker->randomElement(['tunai', 'transfer', 'e_wallet', 'lainnya']),
            'keterangan' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}