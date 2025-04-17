<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanListUsers(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/user');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'email']
            ]);
    }

    public function testCanCreateUser(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/user', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function testCanShowUser(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/user/{$user->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function testCanUpdateUser(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
        ];

        $response = $this->putJson("/api/user/{$user->id}", $data);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updated@example.com',
        ]);
    }

    public function testCanDeleteUser(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/user/{$user->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'message' => 'Usuário excluído com sucesso.',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function testCannotShowNonExistentUser(): void
    {
        $response = $this->getJson('/api/user/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'error' => 'Usuário não encontrado.',
            ]);
    }

    public function testCannotDeleteNonExistentUser(): void
    {
        $response = $this->deleteJson('/api/user/999');

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'error' => 'Usuário não encontrado.',
            ]);
    }
}
