<?php

namespace Tests\Feature\Tenants;

use App\Models\Tenants\Event;
use App\Services\Tenants\FlightService;
use Carbon\Carbon;
use Tests\TenantTestCase;

class FlightTest extends TenantTestCase
{
    /**
     * @var FlightService
     */
    protected $flightService;

    protected function setUp()
    {
        parent::setUp();

        $this->flightService = new FlightService();
    }

    public function testItCanCreateAFlight()
    {
        $this->createTenant();
        $this->loggedInAdminUser();

        $event = factory(Event::class)->create([
            'slug' => 'event-one'
        ]);

        $flights = $this->flightService->getAllForEvent($event);
        $this->assertCount(0, $flights);

        $response = $this->post($this->prepareTenantUrl('office/events/event-one/flights'), [
            'event_id' => $event->id,
            'callsign' => 'ABC123',
            'origin_airport_icao' => 'ABCD',
            'destination_airport_icao' => 'EFGH',
            'departure_time' => Carbon::now()->toTimeString(),
            'arrival_time' => Carbon::now()->addHours(2)->toTimeString(),
            'route' => 'ABCDE1 ABC ABC ABC ABCDE2',
            'aircraft_type_icao' => 'B734'
        ]);

        $flights = $this->flightService->getAllForEvent($event->fresh());

        $this->assertCount(1, $flights);
        $response->assertRedirect('office/events/event-one/flights');
    }
}
