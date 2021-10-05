<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = ($this->faker->dateTimeThisMonth('18:00'))->format('Y-m-d');
        $availableTimes = [];
        $availableTimes[0] = random_int(10,13); // Entre las 10 y las 13 horas
        $availableTimes[1] = random_int(15,17); // Entre las 15 y las 17 horas
        $timeForThis = $availableTimes[random_int(0,1)];

        return [
            'user_asigned' => User::all()->random()->id,
            'title' => $this->faker->sentence(10),
            'slug' => Str::random(8),
            'description' => $this->faker->paragraph(),
            'date_start' => $date,
            'date_end' => $date,
            'hour_start' => $timeForThis.':00:00',
            'hour_end' => ($timeForThis+1).':00:00',
            'duration' => 60,
            'scheduled' => random_int(0,1),
            'finished' => random_int(0,1)
        ];
    }
}
