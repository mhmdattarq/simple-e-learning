<?php

use App\Enums\CourseStatus;
use App\Enums\Role;
use App\Livewire\Auth\Login;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::create([
        'name' => 'Kategori Redirect',
        'slug' => 'kategori-redirect',
    ]);

    $this->course = Course::create([
        'title' => 'Kelas Kursus Target Redirect',
        'slug' => 'kelas-target-redirect',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Published,
    ]);

    $this->user = User::create([
        'name' => 'Peserta Uji Redirect',
        'email' => 'peserta.redirect@gmail.com',
        'password' => Hash::make('password123'),
        'role' => Role::Peserta,
        'email_verified_at' => now(),
    ]);
});

test('login dengan parameter redirect langsung mengarahkan peserta ke url materi kelas tujuan', function () {
    $materiUrl = route('peserta.materi', $this->course->id);

    Livewire::withQueryParams(['redirect' => $materiUrl])
        ->test(Login::class)
        ->set('identifier', 'peserta.redirect@gmail.com')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect($materiUrl);
});

test('login biasa tanpa parameter redirect mengarahkan peserta ke landing page', function () {
    Livewire::test(Login::class)
        ->set('identifier', 'peserta.redirect@gmail.com')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('landing'));
});

test('halaman detail kelas menampilkan tombol masuk dengan parameter redirect ke materi', function () {
    $response = $this->get(route('landing.kelas.detail', $this->course));

    $response->assertStatus(200);
    $response->assertSee(e(route('login', ['redirect' => route('peserta.materi', $this->course)])));
});
