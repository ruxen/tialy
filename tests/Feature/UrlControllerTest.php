<?php

namespace Tests\Feature;

use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UrlControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_original_url()
    {
        $url = Url::factory()->create();
        $response = $this->get('/' . $url->slug);
        $response->assertStatus(301);
        $response->assertRedirect($url->original_url);
    }

    public function test_get_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this->postJson('/api/token', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'token']);
    }

    public function test_show_the_shortened_urls()
    {
        Url::factory()->count(20)->create();
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/admin/urls');
        $response->assertStatus(200);
    }

    public function test_show_the_specified_shortened_url()
    {
        $url = Url::factory()->create();
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->get('/api/admin/urls/' . $url->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }

    public function test_create_and_return_the_shortened_url()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->postJson('/api/admin/urls', [
            'original_url' => 'https://www.techinasia.com',
            'slug' => 'tialy'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['status', 'message', 'shortened_url']);
    }

    public function test_update_the_specified_shortened_url()
    {
        $url = Url::factory()->create();
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->putJson('/api/admin/urls/' . $url->id, [
            'original_url' => 'https://www.updated.com',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['status', 'message']);
    }

    public function test_delete_the_specified_shortened_url()
    {
        $url = Url::factory()->create();
        $user = User::factory()->create();
        Passport::actingAs($user);
        $response = $this->deleteJson('/api/admin/urls/' . $url->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'message']);
    }
}
