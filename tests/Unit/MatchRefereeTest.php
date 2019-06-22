<?php

namespace Tests\Unit;

use App\Events\MoveRecorded;
use App\Match;
use App\MatchReferee;
use App\Move;
use App\Tile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MatchRefereeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_allows_players_to_attempt_moves()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->states('vsHuman')->create();
        $referee = new MatchReferee($match);

        // Pre-check
        $this->assertEquals(0, $match->moves()->count());

        // Execute
        $referee->attemptMove($match->player1, new Tile('A', 1));

        // Check
        $this->assertEquals(1, $match->moves()->count());
        $this->assertTrue($match->moves()->first()->wasMadeBy($match->player1));
    }

    /** @test */
    function it_records_a_computer_turn_after_a_player_turn()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->create();
        $referee = new MatchReferee($match);

        // Pre-check
        $this->assertEquals(0, $match->moves()->count());

        // Execute
        $referee->attemptMove($match->player1, new Tile('A', 1));

        // Check
        $this->assertEquals(2, $match->moves()->count());
        $moves = $match->moves()->inOrder()->get();
        $this->assertTrue($moves->first()->wasMadeBy($match->player1));
        $this->assertTrue($moves->last()->wasMadeByComputer());
    }

    /** @test */
    function it_does_not_allow_a_tile_to_be_occupied_twice()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->create();
        $referee = new MatchReferee($match);

        // Execute & Check
        $referee->attemptMove($match->player1, new Tile('A', 1));
        $this->expectException(ValidationException::class);
        $referee->attemptMove($match->player1, new Tile('A', 1));
    }

    /** @test */
    function it_does_not_allow_a_move_to_be_added_to_an_ended_match()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->create(['ended_at' => now()]);
        $referee = new MatchReferee($match);

        // Execute & Check
        $this->expectException(ValidationException::class);
        $referee->attemptMove($match->player1, new Tile('A', 1));
    }

    /** @test */
    function it_does_not_allow_a_player_to_go_twice_in_a_row()
    {
        // Arrange
        Event::fake();
        $match = factory(Match::class)->create();
        $referee = new MatchReferee($match);

        // Execute & Check
        $referee->attemptMove($match->player1, new Tile('A', 1));
        $this->expectException(ValidationException::class);
        $referee->attemptMove($match->player1, new Tile('A', 2));
    }
}
