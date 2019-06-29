<template>
    <div class="board">
        <div class="winner h2" v-if="winnerFullName">Winner: {{ winnerFullName }}</div>
        <div class="board-row" v-for="row in [1,2,3]">
            <div :class="`tile${readOnly ? ' read-only' : ''}`" v-for="column in ['A','B','C']"
                 @click="attemptMove(column, row)"
            >
                {{ getMark(column, row) }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            matchId: Number,
            playerxId: Number,
            winnerName: String,
            movesMade: Array,
            readOnly: Boolean
        },

        data() {
            return {
                moves: this.movesMade,
                winnerFullName: this.winnerName
            };
        },

        created() {
            Echo.channel(`App.Match.${this.matchId}`)
                .listen('MoveRecorded', event => this.moves.push(event.move))
                .listen('MatchEnded', event => {
                    new Noty({type: 'success', text: 'The match has ended'}).show();
                    if (event.winner) {
                        this.winnerFullName = event.winner.name;
                    }
                });
        },

        methods: {
            attemptMove(column, row) {
                if (this.readOnly) {
                    return;
                }

                axios.post(`/api/matches/${this.matchId}/move`, {column, row})
                    .catch(error => {
                        let message = error.response.data.message;

                        if (error.response.data.errors) {
                            // Just pull the first error.
                            const firstErrorKey = Object.keys(error.response.data.errors)[0];
                            message = error.response.data.errors[firstErrorKey][0];
                        }

                        new Noty({type: 'error', text: message}).show();
                    });
            },

            getMark(column, row) {
                const move = this.moves.find(move => move.column === column && Number(move.row) === row);

                if (move) {
                    return Number(move.player_id) === this.playerxId ? 'X' : 'O';
                }

                return null;
            }
        }
    }
</script>

<style scoped>
    .board {
        min-height: 300px;
        max-height: 600px;
        min-width: 300px;
        max-width: 600px;
    }

    .board-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        min-height: 100px;
        max-height: 200px;
    }

    .board-row .tile {
        border-right: 1px solid black;
        border-bottom: 1px solid black;
        font-size: 3rem;
        text-align: center;
        cursor: pointer;
    }

    .board-row .tile.read-only {
        cursor: default;
    }

    .board-row .tile:last-child {
        border-right: none;
    }
</style>
