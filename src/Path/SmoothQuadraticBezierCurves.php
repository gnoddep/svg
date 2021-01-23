<?php
namespace Nerdman\Svg\Path;

class SmoothQuadraticBezierCurves extends AbstractCoordinatesCommand
{
    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'T' : 't';
    }
}
