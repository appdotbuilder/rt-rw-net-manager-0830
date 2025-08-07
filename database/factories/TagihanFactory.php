<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tagihan>
 */
class TagihanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Tagihan>
     */
    protected $model = Tagihan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        $periode = $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m');
        $jatuhTempo = $this->faker->dateTimeBetween('-1 month', '+1 month');
        
        return [
            'customer_id' => $customer->id,
            'periode' => $periode,
            'jumlah' => $customer->paket->harga ?? $this->faker->numberBetween(50000, 500000),
            'jatuh_tempo' => $jatuhTempo,
            'status' => $this->faker->randomElement(['belum_lunas', 'lunas', 'terlambat']),
            'keterangan' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the bill is unpaid.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'belum_lunas',
        ]);
    }

    /**
     * Indicate that the bill is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lunas',
        ]);
    }

    /**
     * Indicate that the bill is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terlambat',
            'jatuh_tempo' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}