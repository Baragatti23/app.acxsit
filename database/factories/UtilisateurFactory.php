<?php

namespace Database\Factories;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name"=>$this->faker->name(),
            "lastname"=>$this->faker->lastName(),
            "email"=>$this->faker->email(),
            "profile"=>$this->faker->randomElement(["USER","ADMIN"]),
            "password"=>$this->faker->password(8,32)
        ];
    }
}
