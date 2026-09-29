<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('favicon route returns 200 ok and correct file', function () {
    $response = $this->get('/favicon.ico');
    $response->assertOk();
});

test('landing page contains favicon and official logo', function () {
    $response = $this->get(route('landing'));
    $response->assertOk()
        ->assertSee('mine/logo_aceh_timur.webp')
        ->assertSee('favicon.ico');
});

test('admin dashboard contains favicon and official logo', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertOk()
        ->assertSee('mine/logo_aceh_timur.webp')
        ->assertSee('favicon.ico');
});

test('login page contains favicon and official logo', function () {
    $response = $this->get(route('login'));
    $response->assertOk()
        ->assertSee('mine/logo_aceh_timur.webp')
        ->assertSee('favicon.ico');
});
