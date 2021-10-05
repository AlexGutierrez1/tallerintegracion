<?php

namespace Database\Factories;

use App\Models\User;
use Freshwork\ChileanBundle\Rut;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Generar un número aleatorio entre 12.000.000 y 25.000.000
        $random_number = rand(12000000, 25000000);
        // Crear un RUT a partir de lo anterior, sin dígito de verificación
        $rut = new Rut($random_number);
        // Luego, este "$rut" se arregla con la función "fix()", agregando el dígito verificador y se "normaliza"
        // Pasando de, por ejemplo, 55.555.555-5 a 555555555

        return [
            'rut' => $rut->fix()->normalize(),
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'preferred_color' => $this->faker->hexColor(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
