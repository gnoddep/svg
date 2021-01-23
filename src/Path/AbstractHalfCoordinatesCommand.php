<?php
namespace Nerdman\Svg\Path;

use Nerdman\Svg\SvgException;

abstract class AbstractHalfCoordinatesCommand implements PathElementInterface
{
    /** @var array<float> */
    private array $halfCoordinates;
    private bool $absolute;

    abstract protected function getPrefix(bool $absolute): string;

    /**
     * @param array<float> $halfCoordinates
     */
    public function __construct(array $halfCoordinates, bool $absolute)
    {
        if (count($halfCoordinates) < 1) {
            throw new SvgException($this->getPrefix($absolute) . ' requires at least 1 half coordinate');
        }

        $this->halfCoordinates = $halfCoordinates;
        $this->absolute = $absolute;
    }

    public function addCoordinate(float $halfCoordinate): self
    {
        $this->halfCoordinates[] = $halfCoordinate;
    }

    public function __toString(): string
    {
        return $this->getPrefix($this->absolute) . implode(' ', array_map(
                    static fn($coordinate) => number_format($coordinate['x']),
                    $this->halfCoordinates
                )
            );
    }
}
