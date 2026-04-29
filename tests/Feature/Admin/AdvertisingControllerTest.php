<?php

use App\Models\Advertising;
use App\Models\Event;
use App\Models\User;

describe('Admin advertising controller', function () {
    beforeEach(function () {
        $this->admin = User::factory()->admin()->create();
    });

    it('shows advertising index', function () {
        Advertising::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.advertising.index'))
            ->assertOk()
            ->assertSee('advertising');
    });

    it('shows create form', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.advertising.create'))
            ->assertOk();
    });

    it('creates an advertisement', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.advertising.store'), [
                'message' => 'Buy our sponsor product!',
                'active' => true,
            ])
            ->assertRedirect(route('admin.advertising.index'));

        $this->assertDatabaseHas('advertising', [
            'message' => 'Buy our sponsor product!',
            'active' => true,
        ]);
    });

    it('creates an advertisement linked to an event', function () {
        $event = Event::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.advertising.store'), [
                'event_id' => $event->id,
                'message' => 'Event specific ad',
                'active' => false,
            ])
            ->assertRedirect(route('admin.advertising.index'));

        $this->assertDatabaseHas('advertising', [
            'event_id' => $event->id,
            'message' => 'Event specific ad',
        ]);
    });

    it('rejects missing message', function () {
        $this->actingAs($this->admin)
            ->post(route('admin.advertising.store'), ['active' => true])
            ->assertSessionHasErrors('message');
    });

    it('shows edit form', function () {
        $ad = Advertising::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.advertising.edit', $ad))
            ->assertOk();
    });

    it('updates an advertisement', function () {
        $ad = Advertising::factory()->create(['message' => 'Old message']);

        $this->actingAs($this->admin)
            ->put(route('admin.advertising.update', $ad), [
                'message' => 'New message',
                'active' => false,
            ])
            ->assertRedirect(route('admin.advertising.index'));

        $this->assertDatabaseHas('advertising', [
            'id' => $ad->id,
            'message' => 'New message',
        ]);
    });

    it('deletes an advertisement', function () {
        $ad = Advertising::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.advertising.destroy', $ad))
            ->assertRedirect(route('admin.advertising.index'));

        $this->assertDatabaseMissing('advertising', ['id' => $ad->id]);
    });

    it('toggles advertisement active status', function () {
        $ad = Advertising::factory()->create(['active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.advertising.deactivate', $ad))
            ->assertRedirect();

        $this->assertDatabaseHas('advertising', [
            'id' => $ad->id,
            'active' => false,
        ]);
    });

    it('toggles advertisement back to active', function () {
        $ad = Advertising::factory()->inactive()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.advertising.deactivate', $ad))
            ->assertRedirect();

        $this->assertDatabaseHas('advertising', [
            'id' => $ad->id,
            'active' => true,
        ]);
    });

    it('blocks guests from accessing advertising', function () {
        $this->get(route('admin.advertising.index'))->assertRedirect(route('login'));
    });
});
