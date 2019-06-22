<?php

namespace Tests\Unit;

use App\ComputerPlayer;
use App\Events\MoveRecorded;
use App\Match;
use App\MatchReferee;
use App\Move;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComputerPlayerTest extends TestCase
{
    /** @test */
    function it_makes_moves_for_the_computer()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->create();

        // Pre-Check
        $this->assertEquals(9, $match->openTiles()->count());

        // Execute
        $move = (new ComputerPlayer)->makeMove($match);

        // Check
        $this->assertTrue($move instanceof Move);
        $this->assertEquals(1, $match->moves()->count());
        $this->assertEquals(8, $match->openTiles()->count());
        Event::assertDispatched(MoveRecorded::class, function ($e) use ($move) {
            return $e->move->id === $move->id;
        });
    }
}
