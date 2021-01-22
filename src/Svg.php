<?php
namespace Nerdman\Svg;

class Svg
{
    private ?Svg $parent;
    /** @var string[] */
    private array $svgAttributes = [];
    /** @var string[] */
    private array $svgElements = [];
    /** @var Svg[] */
    private array $identifiableGroups = [];

    public function __construct(?self $parent = null)
    {
        $this->parent = $parent;
        if ($this->parent === null) {
            $this->addAttribute('xmlns', 'http://www.w3.org/2000/svg');
        }
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function __toString(): string
    {
        $svg = $this->svgElements;

        $attributes = implode(' ', $this->svgAttributes);

        if ($this->parent) {
            array_unshift($svg, sprintf('<g%s%s>', count($this->svgAttributes) ? ' ' : '', $attributes));
            $svg[] = '</g>';
        } else {
            array_unshift(
                $svg,
                '<?xml version="1.0" encoding="utf-8"?>',
                sprintf('<svg %s>', $attributes)
            );
            $svg[] = '</svg>';
        }

        return implode("\n", $svg);
    }

    public function addAttribute(string $key, string $value): self
    {
        $this->svgAttributes[] = sprintf('%s="%s"', $key, $this->escape($value ?? ''));
        return $this;
    }

    public function addViewBox(float $topLeftX, float $topLeftY, float $bottomRightX, float $bottomRightY): self
    {
        return $this->addAttribute(
            'viewBox',
            sprintf(
                '%d %d %d %d',
                floor($topLeftX),
                floor($topLeftY),
                ceil($bottomRightX) - floor($topLeftX),
                ceil($bottomRightY) - floor($topLeftY)
            )
        );
    }

    public function addDimensions(float $width, float $height, int $precision = 10): self
    {
        return $this
            ->addAttribute('width', number_format(ceil($width), $precision))
            ->addAttribute('height', number_format(ceil($height), $precision));
    }

    public function addNamespace(string $namespace): self
    {
        return $this->addAttribute('xmlns', $namespace);
    }

    /**
     * @param array<string, string> $attributes
     */
    public function addGroup(array $attributes): self
    {
        $group = new Svg($this);

        foreach ($attributes as $key => $value) {
            $group->addAttribute($key, $value);
        }

        $this->svgElements[] = $group;

        if (isset($attributes['id'])) {
            $this->identifiableGroups[$attributes['id']] = $group;
        }

        return $this;
    }

    public function getGroup(string $id): ?self
    {
        return $this->identifiableGroups[$id] ?? null;
    }

    /**
     * @param array<string, string> $attributes
     */
    public function addLine(
        float $startX,
        float $startY,
        float $endX,
        float $endY,
        array $attributes = [],
        bool $useDefaults = true
    ): self {
        if ($useDefaults) {
            $attributes += [
                'stroke' => '#000',
                'stroke-width' => '1',
            ];
        }

        $this->svgElements[] = sprintf(
            '<line x1="%f" y1="%f" x2="%f" y2="%f"%s%s/>',
            $startX,
            $startY,
            $endX,
            $endY,
            count($attributes) ? ' ' : '',
            $this->implodeAttributes($attributes)
        );

        return $this;
    }

    /**
     * @param array<string, string> $attributes
     */
    public function addCircle(
        float $x,
        float $y,
        float $radius,
        array $attributes = [],
        bool $useDefaults = true
    ): self {
        if ($useDefaults) {
            $attributes += [
                'stroke-color' => '#000000',
                'stroke-width' => '1',
                'fill' => '#000000',
            ];
        }

        $title = $attributes['title'] ?? null;
        unset($attributes['title']);

        $arguments = [
            $x,
            $y,
            $radius,
            count($attributes) ? ' ' : '',
            $this->implodeAttributes($attributes),
        ];

        if ($title) {
            $circleSvg = '<circle cx="%f" cy="%f" r="%f"%s%s><title>%s</title></circle>';
            $arguments[] = $title;
        } else {
            $circleSvg = '<circle cx="%f" cy="%f" r="%f"%s%s/>';
        }

        $this->svgElements[] = vsprintf($circleSvg, $arguments);

        return $this;
    }

    /**
     * @param array<string, string> $attributes
     */
    public function addText(
        string $text,
        float $x,
        float $y,
        array $attributes = [],
        bool $useDefaults = true
    ): self {
        if ($useDefaults) {
            $attributes += [
                'font-family' => 'sans-serif',
                'font-size' => '12px',
                'fill' => '#000000',
            ];
        }

        $title = $attributes['title'] ?? null;
        unset($attributes['title']);

        $arguments = [
            $x,
            $y,
            count($attributes) ? ' ' : '',
            $this->implodeAttributes($attributes),
            $this->escape($text),
        ];

        if ($title) {
            $textSvg = '<text x="%f" y="%f"%s%s>%s<title>%s</title></text>';
            $arguments[] = $title;
        } else {
            $textSvg = '<text x="%f" y="%f"%s%s>%s</text>';
        }

        $this->svgElements[] = vsprintf($textSvg, $arguments);

        return $this;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function implodeAttributes(array $attributes): string
    {
        $attr = [];

        foreach ($attributes as $key => $value) {
            if ($value !== null) {
                $attr[] = sprintf('%s="%s"', $key, $this->escape($value));
            }
        }

        return implode(' ', $attr);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
