<?php

use App\Models\User;

describe('Admin middleware', function () {
    it('allows super admin access', function () {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    });

    it('blocks non-admin users with 403', function () {
        $user = User::factory()->create(['is_super_admin' => false]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    it('redirects guests to login', function () {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    });
});
