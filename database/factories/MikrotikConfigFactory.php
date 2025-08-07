<?php

namespace Database\Factories;

use App\Models\MikrotikConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MikrotikConfig>
 */
class MikrotikConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<MikrotikConfig>
     */
    protected $model = MikrotikConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Main Router',
            'host' => $this->faker->localIpv4(),
            'port' => 8728,
            'username' => 'admin',
            'password' => 'admin123',
            'is_active' => true,
            'description' => 'Main Mikrotik router for RT/RW network',
        ];
    }
}