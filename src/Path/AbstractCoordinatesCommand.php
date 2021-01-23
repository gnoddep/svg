<?php
namespace Nerdman\Svg\Path;

use Nerdman\Svg\SvgException;

abstract class AbstractCoordinatesCommand implements PathElementInterface
{
    /** @var array<array{x: float, y: float}> */
    private array $coordinates;
    private bool $absolute;

    abstract protected function getPrefix(bool $absolute): string;

    /**
     * @param array<array{x: float, y: float}> $coordinates
     */
    public function __construct(array $coordinates, bool $absolute)
    {
        if (count($coordinates) < 1) {
            throw new SvgException($this->getPrefix($absolute) . ' requires at least 1 coordinate');
        }

        $this->coordinates = $coordinates;
        $this->absolute = $absolute;
    }

    public function addCoordinate(float $x, float $y): self
    {
        $this->coordinates[] = ['x' => $x, 'y' => $y];
    }

    public function __toString(): string
    {
        return $this->getPrefix($this->absolute) . implode(' ', array_map(
                    static fn($coordinate) => number_format($coordinate['x']) . ',' . number_format($coordinate['y']),
                    $this->coordinates
                )
            );
    }
}
