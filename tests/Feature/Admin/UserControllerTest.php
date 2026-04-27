<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\User;

describe('Admin user controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows user index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    });

    it('creates a user', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'username'              => 'newuser',
                'email_address'         => 'new@example.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sf_guard_user', ['username' => 'newuser']);
    });

    it('requires unique username', function () {
        User::factory()->create(['username' => 'taken']);

        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'username'              => 'taken',
                'email_address'         => 'other@example.com',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertSessionHasErrors('username');
    });

    it('updates a user without changing password', function () {
        $user = User::factory()->create(['first_name' => 'Old']);

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'username'      => $user->username,
                'email_address' => $user->email_address,
                'first_name'    => 'New',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('sf_guard_user', ['id' => $user->id, 'first_name' => 'New']);
    });

    it('updates a user password when provided', function () {
        $user = User::factory()->create();
        $oldHash = $user->password;

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), [
                'username'              => $user->username,
                'email_address'         => $user->email_address,
                'password'              => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('sf_guard_user', ['id' => $user->id, 'password' => $oldHash]);
    });

    it('deletes another user', function () {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($user);
    });

    it('cannot delete own account', function () {
        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertModelExists($this->admin);
    });
});
