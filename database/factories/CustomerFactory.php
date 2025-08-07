<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PaketInternet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Customer>
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = $this->faker->name();
        
        return [
            'nama' => $nama,
            'alamat' => $this->faker->address(),
            'kontak' => $this->faker->phoneNumber(),
            'username_pppoe' => Customer::generateUsername($nama),
            'password_pppoe' => Customer::generatePassword(),
            'paket_id' => PaketInternet::factory(),
            'ip_pool' => $this->faker->optional(0.7)->ipv4(),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif', 'suspended']),
            'tanggal_daftar' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'keterangan' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the customer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'nonaktif',
        ]);
    }
}