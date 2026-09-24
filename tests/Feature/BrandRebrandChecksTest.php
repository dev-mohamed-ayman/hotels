<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

/**
 * Brand rebrand render-assertion checks (T035).
 *
 * Lightweight regression guards for the Azha brand cascade hooks and brand
 * labels. Visual pixel/color validation is covered by manual quickstart
 * checks, not by this file.
 *
 * NOTE: The login page lives at the locale root (GET /en, /ar) in this
 * application — there is no /en/login route — so the assertions below target
 * the real login URLs.
 */
uses(RefreshDatabase::class);

test('login page wires the Azha brand cascade and English welcome copy', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('assets/css/azha-brand.css', false);
    $response->assertSee('Welcome to AZHA Travel!', false);
});

test('login page wires the Azha brand cascade and Arabic welcome copy', function () {
    app()->setLocale('ar');
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('assets/css/azha-brand.css', false);
    $response->assertSee('أهلاً بك في AZHA Travel!', false);
});

test('protected admin shell renders the Azha brand cascade and brand text', function () {
    Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->givePermissionTo('view dashboard');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('assets/css/azha-brand.css', false);
    $response->assertSee('AZHA', false);
});
