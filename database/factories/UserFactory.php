<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nip' => fake()->unique()->numerify('19##########01####'),
            'role' => Role::Peserta,
            'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
            'position' => 'Staf Pelaksana',
            'rank_class' => 'Penata Muda (III/a)',
            'phone_number' => '08'.fake()->numerify('##########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Admin role state.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Admin,
            'position' => 'Administrator Diklat BKPSDM',
        ]);
    }

    /**
     * Mentor role state.
     */
    public function mentor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Mentor,
            'position' => 'Widyaiswara Ahli Madya',
        ]);
    }

    /**
     * Verifikator role state.
     */
    public function verifikator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Verifikator,
            'position' => 'Petugas Verifikator Berkas',
        ]);
    }

    /**
     * Pimpinan role state.
     */
    public function pimpinan(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Pimpinan,
            'position' => 'Kepala BKPSDM Aceh Timur',
        ]);
    }

    /**
     * Peserta role state.
     */
    public function peserta(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => Role::Peserta,
            'position' => 'Peserta Pelatihan ASN',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
