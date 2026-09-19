<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('login page can be accessed and assets are correctly routed', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('admin/assets/images/favicon.png');
    $response->assertSee('admin/assets/css/remixicon.css');
    $response->assertSee('admin/assets/css/lib/bootstrap.min.css');
    $response->assertSee('admin/assets/css/style.css');
    $response->assertSee('admin/assets/js/lib/jquery-3.7.1.min.js');
    $response->assertSee('admin/assets/js/app.js');
    $response->assertSee('SIMPEL');
    $response->assertSee('BKPSDM Aceh Timur');
    $response->assertSee('Portal Pelatihan Mandiri Aparatur Sipil Negara');
    $response->assertSee('Masuk ke Akun');
    $response->assertDontSee('editor-katex.min.css');
});

test('login component can authenticate user successfully', function () {
    $user = User::factory()->create([
        'email' => 'asn@acehtimurkab.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'asn@acehtimurkab.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('login component validates required fields', function () {
    Livewire::test(Login::class)
        ->set('email', '')
        ->set('password', '')
        ->call('authenticate')
        ->assertHasErrors(['email', 'password']);
});
