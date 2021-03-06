<?php

namespace Tests\Feature;

use App\Models\BookableFlight;
use App\Models\Booker;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use RachidLaasri\Travel\Travel;
use Tests\TestCase;

class DisplayBookablesTest extends TestCase
{
    use RefreshDatabase;

    protected $tenancy = true;

    /** @test */
    public function it_does_not_immediately_book()
    {
        Travel::to(now());

        $event = factory(Event::class)->create([
            'slug' => 'foo-event',
        ]);
        $flight = factory(BookableFlight::class)->create([
            'event_id' => $event->id,
        ]);
        $booker = factory(Booker::class)->create([
            'email' => 'foo@example.org',
        ]);

        Livewire::test('display-bookables', ['event' => $event])
            ->set('email', $booker->email)
            ->call('bookBookable', $flight->id);

        $this->assertDatabaseMissing('bookables', [
            'id' => $flight->id,
            'event_id' => $event->id,
            'booked_by_id' => $booker->id,
            'booked_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Travel::back();
    }

    /** @test */
    public function it_wont_book_already_booked_bookables()
    {
        Travel::to(now());

        $event = factory(Event::class)->create([
            'slug' => 'foo-event',
        ]);
        $bookerOne = factory(Booker::class)->create([
            'email' => 'foo@example.org',
        ]);
        $bookerTwo = factory(Booker::class)->create([
            'email' => 'bar@example.org',
        ]);
        $flight = factory(BookableFlight::class)->create([
            'event_id' => $event->id,
            'booked_at' => now(),
            'booked_by_id' => $bookerOne->id,
        ]);

        Livewire::test('display-bookables', ['event' => $event])
            ->set('email', $bookerTwo->email)
            ->call('bookBookable', $flight->id);

        $this->assertDatabaseHas('bookables', [
            'id' => $flight->id,
            'event_id' => $event->id,
            'booked_by_id' => $bookerOne->id,
            'booked_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Travel::back();
    }

    /** @test */
    public function it_creates_a_booker_record_for_new_bookers()
    {
        $event = factory(Event::class)->create();
        $flight = factory(BookableFlight::class)->create([
            'event_id' => $event->id,
        ]);

        Livewire::test('display-bookables', ['event' => $event])
            ->set('email', 'foo@example.org')
            ->call('bookBookable', $flight->id);

        $this->assertDatabaseHas('bookers', [
            'email' => 'foo@example.org',
        ]);
    }

    /** @test */
    public function only_bookables_for_a_specific_event_is_shown()
    {
        $eventOne = factory(Event::class)->create();
        $eventOneFlights = factory(BookableFlight::class, 3)->create([
            'event_id' => $eventOne->id,
        ]);
        $eventTwo = factory(Event::class)->create();
        $eventTwoFlights = factory(BookableFlight::class, 4)->create([
            'event_id' => $eventTwo->id,
        ]);

        $displayBookablesComponent = Livewire::test('display-bookables', ['event' => $eventOne]);

        $bookables = $displayBookablesComponent->viewData('bookables');

        $this->assertCount(3, $bookables);
        $eventOneFlights->assertEquals($bookables);
    }
}
