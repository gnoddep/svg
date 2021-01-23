<?php
namespace Nerdman\Svg\Path;

class LineTo extends AbstractCoordinatesCommand
{
    protected function getPrefix(bool $absolute): string
    {
        return $absolute ? 'L' : 'l';
    }
}
