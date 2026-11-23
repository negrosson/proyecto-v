<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->randomElement(array_keys(Customer::TYPES)),
            'rut' => fake()->optional()->numerify('##.###.###-#'),
            'phone' => '+56 9 '.fake()->numerify('#### ####'),
            'email' => fake()->unique()->safeEmail(),
            'commune' => fake()->randomElement(['Las Condes', 'Providencia', 'Vitacura', 'Ñuñoa', 'Lo Barnechea', 'Maipú']),
            'is_frequent' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function frequent(): static
    {
        return $this->state(fn (): array => [
            'is_frequent' => true,
        ]);
    }

    public function company(): static
    {
        return $this->state(fn (): array => [
            'type' => Customer::TYPE_EMPRESA,
        ]);
    }
}
