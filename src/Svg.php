<?php
namespace Nerdman\Svg;

class Svg
{
    private ?Svg $parent;
    /** @var array<string, string> */
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
        $elements = \implode('', $this->svgElements);

        if ($this->parent) {
            return $this->createTag('g', $this->svgAttributes, $elements);
        } else {
            return '<?xml version="1.0" encoding="utf-8"?>'
                . "\n"
                . $this->createTag('svg', $this->svgAttributes, $elements);
        }
    }

    public function addAttribute(string $key, string $value): self
    {
        $this->svgAttributes[$key] = $value;
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

    public function addDimensions(float $width, float $height, int $precision = 0): self
    {
        return $this
            ->addAttribute('width', number_format(ceil($width), $precision))
            ->addAttribute('height', number_format(ceil($height), $precision));
    }

    public function addNamespace(string $prefix, string $namespace): self
    {
        return $this->addAttribute('xmlns:' . $prefix, $namespace);
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

        $attributes['x1'] = number_format($startX);
        $attributes['y1'] = number_format($startY);
        $attributes['x2'] = number_format($endX);
        $attributes['y2'] = number_format($endY);

        $this->svgElements[] = $this->createTag('line', $attributes);

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
                'stroke' => '#000',
                'stroke-width' => '1',
                'fill' => '#000',
            ];
        }

        if (isset($attributes['title'])) {
            $title = $this->createTag('title', [], $attributes['title']);
            unset($attributes['title']);
        }

        $attributes['cx'] = number_format($x);
        $attributes['cy'] = number_format($y);
        $attributes['r'] = number_format($radius);

        $this->svgElements[] = $this->createTag('circle', $attributes, $title ?? null);

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
                'fill' => '#000',
            ];
        }

        $text = $this->escape($text);

        if (isset($attributes['title'])) {
            $text .= $this->createTag('title', [], $attributes['title']);
            unset($attributes['title']);
        }

        $attributes['x'] = number_format($x);
        $attributes['y'] = number_format($y);

        $this->svgElements[] = $this->createTag('text', $attributes, $text);

        return $this;
    }

    /**
     * @param array<string, string> $attributes
     */
    public function addPath(array $pathElements, array $attributes = [], bool $useDefaults = true): self
    {
        if ($useDefaults) {
            $attributes += [
                'stroke' => '#000',
                'stroke-width' => '1',
                'fill' => 'none',
            ];
        }

        $attributes['d'] = implode(' ', $pathElements);

        $this->svgElements[] = $this->createTag('path', $attributes);

        return $this;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function createTag(string $element, array $attributes = [], ?string $content = null): string
    {
        $tag = '<' . $element;

        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($value !== null) {
                    $tag .= sprintf(' %s="%s"', $key, $this->escape($value));
                }
            }
        }

        if ($content === null) {
            $tag .= '/>';
        } else {
            $tag .= '>' . $content . '</' . $element . '>';
        }

        return $tag;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
