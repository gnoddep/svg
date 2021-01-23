<?php
namespace Nerdman\Svg\Path;

class HorizontalLineTo extends AbstractHalfCoordinatesCommand
{
    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'H' : 'h';
    }
}
