<?php

namespace App;

// We identify each of the tiles like Excel identifies cells.
//  A1 | B1 | C1
//  A2 | B2 | C2
//  A3 | B3 | C3

class Tile
{
    protected $column;
    protected $row;

    public function __construct(string $column, int $row)
    {
        $this->column = $column;
        $this->row = $row;
    }

    /**
     * Build a tile from a given shorthand value (e.g. 'A3')
     *
     * @param string $shorthand
     *
     * @return static
     */
    public static function fromShorthand(string $shorthand)
    {
        if (!static::isValidShorthand($shorthand)) {
            throw new \InvalidArgumentException('Tile shorthand must be in the form <column><row>');
        }

        return new static(
            substr($shorthand, 0, 1),
            substr($shorthand, 1)
        );
    }

    /**
     * Is the given shorthand value valid?
     *
     * @param $shorthand
     *
     * @return bool
     */
    public static function isValidShorthand($shorthand)
    {
        return preg_match('/[A-Z][1-9]+/', $shorthand) === 1;
    }

    /**
     * The shorthand value for this tile.
     *
     * @return string
     */
    public function shorthand()
    {
        return $this->column . $this->row;
    }

    public function column()
    {
        return $this->column;
    }

    public function row()
    {
        return $this->row;
    }
}
