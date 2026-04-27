<?php

use App\Models\Season;
use App\Models\User;

describe('Admin season controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows season index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.seasons.index'))
            ->assertOk();
    });

    it('creates a season', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.seasons.store'), [
                'name' => 'Season 1',
                'active' => true,
            ])
            ->assertRedirect(route('admin.seasons.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('seasons', ['name' => 'Season 1']);
    });

    it('updates a season', function () {
        $season = Season::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('admin.seasons.update', $season), [
                'name' => 'New Name',
                'active' => false,
            ])
            ->assertRedirect(route('admin.seasons.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('seasons', ['id' => $season->id, 'name' => 'New Name']);
    });

    it('deletes a season', function () {
        $season = Season::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.seasons.destroy', $season))
            ->assertRedirect(route('admin.seasons.index'));

        $this->assertModelMissing($season);
    });

    it('toggles season active status', function () {
        $season = Season::factory()->create(['active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.seasons.deactivate', $season))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('seasons', ['id' => $season->id, 'active' => false]);
    });
});
