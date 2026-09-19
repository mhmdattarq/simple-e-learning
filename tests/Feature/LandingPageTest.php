<?php

test('landing page can be accessed successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('landing/assets/css/bootstrap.min.css');
    $response->assertSee('landing/assets/js/script.js');
});

test('admin dashboard can be accessed on /admin and /dashboard', function () {
    $responseAdmin = $this->get('/admin');
    $responseAdmin->assertStatus(200);

    $responseDashboard = $this->get('/dashboard');
    $responseDashboard->assertStatus(200);
});
