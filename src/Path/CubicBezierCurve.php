<?php
namespace Nerdman\Svg\Path;

use Nerdman\Svg\SvgException;

class CubicBezierCurve extends AbstractCoordinatesCommand
{
    /**
     * @param array<array{x: float, y: float}> $coordinates
     */
    public function __construct(array $coordinates, bool $absolute)
    {
        if (\count($coordinates) % 3 !== 0) {
            throw new SvgException($this->getPrefix($absolute) . ' must consist of coordinate triplets');
        }

        parent::__construct($coordinates, $absolute);
    }

    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'C' : 'c';
    }
}