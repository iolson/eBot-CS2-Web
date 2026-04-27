<?php

use App\Models\Config;
use App\Models\User;

describe('Admin config controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows config index', function () {
        Config::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.configs.index'))
            ->assertOk();
    });

    it('shows create form', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.configs.create'))
            ->assertOk();
    });

    it('creates a config', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.configs.store'), [
                'name' => 'match_cfg',
                'content' => 'sv_cheats 0',
            ])
            ->assertRedirect(route('admin.configs.index'));

        $this->assertDatabaseHas('configs', [
            'name' => 'match_cfg',
            'content' => 'sv_cheats 0',
        ]);
    });

    it('rejects duplicate config name', function () {
        Config::factory()->create(['name' => 'existing_cfg']);

        $this->actingAs($this->admin)
            ->post(route('admin.configs.store'), [
                'name' => 'existing_cfg',
                'content' => 'something',
            ])
            ->assertSessionHasErrors('name');
    });

    it('shows edit form', function () {
        $config = Config::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.configs.edit', $config))
            ->assertOk();
    });

    it('updates a config', function () {
        $config = Config::factory()->create(['name' => 'old_name', 'content' => 'old']);

        $this->actingAs($this->admin)
            ->put(route('admin.configs.update', $config), [
                'name' => 'new_name',
                'content' => 'sv_cheats 1',
            ])
            ->assertRedirect(route('admin.configs.index'));

        $this->assertDatabaseHas('configs', [
            'id' => $config->id,
            'name' => 'new_name',
            'content' => 'sv_cheats 1',
        ]);
    });

    it('allows same name on update (unique except self)', function () {
        $config = Config::factory()->create(['name' => 'my_cfg']);

        $this->actingAs($this->admin)
            ->put(route('admin.configs.update', $config), [
                'name' => 'my_cfg',
                'content' => 'updated content',
            ])
            ->assertRedirect(route('admin.configs.index'));
    });

    it('deletes a config', function () {
        $config = Config::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.configs.destroy', $config))
            ->assertRedirect(route('admin.configs.index'));

        $this->assertDatabaseMissing('configs', ['id' => $config->id]);
    });

    it('blocks guests from accessing configs', function () {
        $this->get(route('admin.configs.index'))->assertRedirect(route('login'));
    });
});
