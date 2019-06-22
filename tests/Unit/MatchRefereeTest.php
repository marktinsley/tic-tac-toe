<?php

namespace Tests\Unit;

use App\Match;
use App\MatchReferee;
use App\Move;
use App\Tile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery\Exception;
use Tests\TestCase;

class MatchRefereeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_allows_moves_to_be_attempted_in_a_match()
    {
        // Arrange
        $match = factory(Match::class)->create();
        $referee = new MatchReferee($match);

        // Pre-check
        $this->assertEquals(0, $match->moves()->count());

        // Execute
        $move = $referee->attemptMove($match->player1, new Tile('A', 1));

        // Check
        $this->assertTrue($move instanceof Move);
        $this->assertEquals(1, $match->moves()->count());
    }

    /** @test */
    function it_does_not_allow_a_tile_to_be_occupied_twice()
    {
        // Arrange
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
        $match = factory(Match::class)->create();
        $referee = new MatchReferee($match);

        // Execute & Check
        $referee->attemptMove($match->player1, new Tile('A', 1));
        $this->expectException(ValidationException::class);
        $referee->attemptMove($match->player1, new Tile('A', 2));
    }
}
