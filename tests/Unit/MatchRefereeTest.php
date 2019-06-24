<?php

namespace Tests\Unit;

use App\ComputerPlayer;
use App\Match;
use App\MatchReferee;
use App\Tile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MatchRefereeTest extends TestCase
{
    use RefreshDatabase;

    function setUp(): void
    {
        parent::setUp();
        Event::fake();
        ComputerPlayer::generate();
    }

    private function matchWithMoves($moves)
    {
        $match = factory(Match::class)->state('vsHuman')->create();
        $referee = new MatchReferee($match);

        $x = 0;
        $players = [$match->player1, $match->player2];
        foreach ($moves as $tileShorthand) {
            try {
                $referee->attemptMove($players[$x % 2], Tile::fromShorthand($tileShorthand));
            } catch (ValidationException $e) {
                dd($e->errors());
            }
            $x++;
        }

        return $match;
    }

    /** @test */
    function it_allows_players_to_attempt_moves()
    {
        // Arrange
        $match = factory(Match::class)->state('vsHuman')->create();
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
        $match = factory(Match::class)->state('vsHuman')->create();
        $referee = new MatchReferee($match);

        // Execute & Check
        $referee->attemptMove($match->player1, new Tile('A', 1));
        $this->expectException(ValidationException::class);
        $referee->attemptMove($match->player1, new Tile('A', 2));
    }

    /** @test */
    function it_finds_vertical_wins()
    {
        // Arrange
        $match1 = $this->matchWithMoves(['A1', 'B2', 'A2', 'C3', 'A3']);
        // X |   |
        // X | O |
        // X |   | O
        $match2 = $this->matchWithMoves(['B2', 'C1', 'A2', 'C2', 'B1', 'B3', 'A1', 'C3']);
        // X | X | O
        // X | X | O
        //   | O | O

        // Execute
        $winner1 = (new MatchReferee($match1))->lookForWinner();
        $winner2 = (new MatchReferee($match2))->lookForWinner();

        // Check
        $this->assertTrue($winner1->is($match1->player1));
        $this->assertTrue($winner2->is($match2->player2));
    }

    /** @test */
    function it_finds_horizontal_wins()
    {
        // Arrange
        $match1 = $this->matchWithMoves(['A1', 'B2', 'B1', 'C3', 'C1']);
        // X | X | X
        //   | O |
        //   |   | O
        $match2 = $this->matchWithMoves(['B2', 'A3', 'A2', 'C2', 'B1', 'B3', 'A1', 'C3']);
        // X | X |
        // X | X | O
        // O | O | O

        // Execute
        $winner1 = (new MatchReferee($match1))->lookForWinner();
        $winner2 = (new MatchReferee($match2))->lookForWinner();

        // Check
        $this->assertTrue($winner1->is($match1->player1));
        $this->assertTrue($winner2->is($match2->player2));
    }

    /** @test */
    function it_finds_cross_wins()
    {
        // Arrange
        $match1 = $this->matchWithMoves(['A2', 'B2', 'B1', 'C3', 'C1', 'A1']);
        // O | X | X
        // X | O |
        //   |   | O
        $match2 = $this->matchWithMoves(['B2', 'A1', 'C3', 'C2', 'B1', 'B3', 'C1', 'A2', 'A3']);
        // O | X | X
        // O | X | O
        // X | O | X

        // Execute
        $winner1 = (new MatchReferee($match1))->lookForWinner();
        $winner2 = (new MatchReferee($match2))->lookForWinner();

        // Check
        $this->assertTrue($winner1->is($match1->player2));
        $this->assertTrue($winner2->is($match2->player1));
    }
}
