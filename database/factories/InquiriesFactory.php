<?php
namespace Database\Factories;

use App\Models\Inquiries;
use Illuminate\Database\Eloquent\Factories\Factory;

class InquiriesFactory extends Factory
{
    protected $model = Inquiries::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'message' => $this->faker->paragraph(),
            'total' => 0, // Calculated later
            'inquiries_status' => $this->faker->randomElement(['approved', 'rejected', 'pending']),
        ];
    }
}
