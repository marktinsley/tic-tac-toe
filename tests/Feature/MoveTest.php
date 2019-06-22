<?php

namespace Tests\Feature;

use App\Match;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MoveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_records_valid_moves()
    {
        // Arrange
        $match = factory(Match::class)->states('vsHuman')->create();

        // Pre-Check
        $this->assertEquals(0, $match->moves()->count());

        // Execute
        $this->post(
            "/api/matches/{$match->id}/move?api_token={$match->player1->api_token}",
            ['column' => 'C', 'row' => 2]
        );

        // Check
        $this->assertEquals(1, $match->moves()->count());
    }
}
