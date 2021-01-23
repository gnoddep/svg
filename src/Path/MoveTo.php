<?php
namespace Nerdman\Svg\Path;

class MoveTo extends AbstractCoordinatesCommand
{
    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'M' : 'm';
    }
}
