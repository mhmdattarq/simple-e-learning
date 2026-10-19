<?php

use App\Models\Category;
use App\Models\CourseExternal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('CourseExternal model creates auto-slug and stores standard length fields correctly', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();

    $external = CourseExternal::create([
        'category_id' => $category->id,
        'name' => 'SiBangkom LAN RI',
        'url' => 'https://sibangkom.lan.go.id',
        'description' => 'Portal pengembangan kompetensi aparatur terpadu.',
        'type' => 'gratis',
        'benefit' => 'Sertifikat',
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    expect($external->slug)->toBe('sibangkom-lan-ri')
        ->and($external->isGratis())->toBeTrue()
        ->and($external->isBerbayar())->toBeFalse()
        ->and($external->isPublished())->toBeTrue()
        ->and($external->category->id)->toBe($category->id)
        ->and($external->creator->id)->toBe($admin->id);
});

test('CourseExternal factory generates valid model instances', function () {
    $item = CourseExternal::factory()->gratis()->create();

    expect($item->id)->not->toBeNull()
        ->and($item->type)->toBe('gratis')
        ->and($item->status)->toBe('published');
});
