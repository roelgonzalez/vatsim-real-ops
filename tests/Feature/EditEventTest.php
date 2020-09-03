<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use RachidLaasri\Travel\Travel;
use Tests\TestCase;

class EditEventTest extends TestCase
{
    use RefreshDatabase;

    protected $tenancy = true;

    /** @test */
    public function it_has_existing_data_in_form_preloaded()
    {
        Travel::to(now());

        $event = factory(Event::class)->create([
            'title' => 'The Event Title',
            'description' => 'Description Here',
            'start_date' => $startDate = now()->format('Y-m-d'),
            'start_time' => $startTime = now()->format('H:i'),
            'end_date' => $endDate = now()->format('Y-m-d'),
            'end_time' => $endTime = now()->addHour()->format('H:i'),
            'banner_url' => 'https://example.org/image.jpg'
        ]);

        Livewire::test('edit-event', ['event' => $event])
            ->assertSet('title', 'The Event Title')
            ->assertSet('description', 'Description Here')
            ->assertSet('startDate', $startDate)
            ->assertSet('startTime', $startTime)
            ->assertSet('endDate', $endDate)
            ->assertSet('endTime', $endTime)
            ->assertSet('bannerUrl', 'https://example.org/image.jpg');

        Travel::back();
    }

    /** @test */
    public function it_can_change_data_on_save()
    {
        Travel::to(now());

        $event = factory(Event::class)->create([
            'title' => 'The Event Title',
            'description' => 'Description Here',
            'start_date' => $startDate = now()->format('Y-m-d'),
            'start_time' => $startTime = now()->format('H:i'),
            'end_date' => $endDate = now()->format('Y-m-d'),
            'end_time' => $endTime = now()->addHour()->format('H:i'),
            'banner_url' => 'https://example.org/image.jpg',
        ]);

        Livewire::test('edit-event', ['event' => $event])
            ->set('title', 'The Changed Event Title')
            ->set('endDate', $newEndDate = now()->addDay()->format('Y-m-d'))
            ->set('endTime', $newEndTime = now()->addDay()->addHour()->format('H:i'))
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('events', [
            'title' => 'The Changed Event Title',
            'description' => 'Description Here',
            'start_date' => $startDate,
            'start_time' => $startTime,
            'end_date' => $newEndDate,
            'end_time' => $newEndTime,
            'banner_url' => 'https://example.org/image.jpg',
        ]);

        Travel::back();
    }

    /** @test */
    public function it_shows_choose_event_bookable_type_if_non_chosen_yet()
    {
        $this->login();

        factory(Event::class)->create([
            'slug' => 'no-bookable-type',
            'bookable_type' => null,
        ]);

        $this->get('office/events/no-bookable-type')
            ->assertSeeLivewire('choose-event-bookable-type')
            ->assertDontSeeLivewire('display-bookables')
            ->assertDontSeeLivewire('add-bookable-flight');
    }
}
