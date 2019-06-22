<template>
    <div class="board">
        <div class="board-row">
            <div class="tile"></div>
            <div class="tile"></div>
            <div class="tile"></div>
        </div>
        <div class="board-row">
            <div class="tile"></div>
            <div class="tile"></div>
            <div class="tile"></div>
        </div>
        <div class="board-row">
            <div class="tile"></div>
            <div class="tile"></div>
            <div class="tile"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {matchId: Number}
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
    }

    .board-row .tile:last-child {
        border-right: none;
    }
</style>
