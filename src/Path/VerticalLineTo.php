<?php
namespace Nerdman\Svg\Path;

class VerticalLineTo extends AbstractHalfCoordinatesCommand
{
    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'V' : 'v';
    }
}
