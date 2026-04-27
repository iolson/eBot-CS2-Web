<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'email_address' => $this->faker->unique()->safeEmail(),
            'username'      => $this->faker->unique()->userName(),
            'algorithm'     => 'bcrypt',
            'salt'          => null,
            'password'      => Hash::make('password'),
            'is_active'     => true,
            'is_super_admin' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(['is_super_admin' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /** Create a user with a legacy SHA-1 password for migration testing */
    public function legacySha1(string $plaintext = 'password'): static
    {
        $salt = md5(rand(100000, 999999) . 'test');

        return $this->state([
            'algorithm' => 'sha1',
            'salt'      => $salt,
            'password'  => sha1($salt . $plaintext),
        ]);
    }
}
