<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tes skenario login berhasil dengan kredensial benar dan status aktif.
     * @test
     */
    public function a_user_can_login_with_correct_credentials_and_active_status(): void
    {
        // 1. Buat pengguna dengan status aktif
        $user = User::factory()->create(['status' => true]);

        // 2. Lakukan request login ke API
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password', // 'password' adalah password default dari factory
        ]);

        // 3. Pastikan response sukses (status 200) dan memiliki struktur JSON yang benar
        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'user']);
    }

    /**
     * Tes skenario login gagal karena status pengguna tidak aktif.
     * @test
     */
    public function an_inactive_user_cannot_login(): void
    {
        // 1. Buat pengguna dengan status tidak aktif
        $user = User::factory()->create(['status' => false]);

        // 2. Lakukan request login ke API
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // 3. Pastikan response ditolak (status 403) dan berisi pesan error yang sesuai
        $response->assertStatus(403)
                 ->assertJson(['message' => 'Akun Anda tidak aktif.']);
    }
}