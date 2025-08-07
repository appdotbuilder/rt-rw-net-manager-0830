<?php

namespace Database\Factories;

use App\Models\PaketInternet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaketInternet>
 */
class PaketInternetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<PaketInternet>
     */
    protected $model = PaketInternet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $speeds = ['5 Mbps', '10 Mbps', '20 Mbps', '30 Mbps', '50 Mbps', '100 Mbps'];
        $speed = $this->faker->randomElement($speeds);
        $speedNumber = intval($speed);
        
        return [
            'nama_paket' => 'Paket ' . $speed,
            'harga' => $speedNumber * 10000, // Base price calculation
            'bandwidth' => $speed,
            'deskripsi' => "Paket internet dengan kecepatan {$speed} cocok untuk " . 
                          ($speedNumber <= 10 ? 'browsing dan streaming' : 
                          ($speedNumber <= 30 ? 'streaming HD dan gaming' : 'kebutuhan bisnis dan streaming 4K')),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
        ];
    }

    /**
     * Indicate that the package is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
        ]);
    }
}