<?php

use App\Models\Event;
use App\Models\User;

describe('Admin event controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows event index', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.events.index'))
            ->assertOk();
    });

    it('creates an event', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.events.store'), [
                'name' => 'LAN Event 1',
                'active' => true,
            ])
            ->assertRedirect(route('admin.events.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', ['name' => 'LAN Event 1']);
    });

    it('updates an event', function () {
        $event = Event::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('admin.events.update', $event), [
                'name' => 'New Name',
                'active' => false,
            ])
            ->assertRedirect(route('admin.events.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', ['id' => $event->id, 'name' => 'New Name']);
    });

    it('deletes an event', function () {
        $event = Event::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.events.destroy', $event))
            ->assertRedirect(route('admin.events.index'));

        $this->assertModelMissing($event);
    });

    it('toggles event active status', function () {
        $event = Event::factory()->create(['active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.events.deactivate', $event))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('events', ['id' => $event->id, 'active' => false]);
    });
});
