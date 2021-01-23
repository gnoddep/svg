<?php
namespace Nerdman\Svg;

use Nerdman\Svg\Path\LineTo;
use Nerdman\Svg\Path\MoveTo;
use PHPUnit\Framework\TestCase;

class SvgTest extends TestCase
{
    /**
     * @param Svg $svg
     * @param string $expected
     *
     * @dataProvider provider
     */
    public function testSvg(Svg $svg, string $expected)
    {
        self::assertXmlStringEqualsXmlString($expected, (string)$svg);
    }

    public function provider(): array
    {
        return [
            'empty' => [
                new Svg(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg"/>
                SVG
            ],
            'viewbox' => [
                (new Svg())->addViewBox(0, 0, 100, 100),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"/>
                SVG
            ],
            'dimensions' => [
                (new Svg())->addDimensions(100, 200),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="200"/>
                SVG
            ],
            'namespace' => [
                (new Svg())->addNamespace('t', 'http://nerdman.nl/test'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:t="http://nerdman.nl/test"/>
                SVG
            ],
            'attribute' => [
                (new Svg())->addAttribute('test', '123'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" test="123"/>
                SVG
            ],
            'attribute_with_special_character' => [
                (new Svg())->addAttribute('test', '"\'<>&'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" test="&quot;&apos;&lt;&gt;&amp;"/>
                SVG
            ],
            'group' => [
                (new Svg())->addGroup([]),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g/>
                </svg>
                SVG
            ],
            'group_with_id' => [
                (new Svg())->addGroup(['id' => 'test', 'another' => 'testtest']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test" another="testtest"/>
                </svg>
                SVG
            ],
            'group_in_group' => [
                (new Svg())->addGroup(['id' => 'test'])->getGroup('test')->addGroup([])->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test">
                        <g/>
                    </g>
                </svg>
                SVG
            ],
            'identifiable_group' => [
                (new Svg())->addGroup(['id' => 'test'])->getGroup('test'),
                <<<'SVG'
                <g id="test"/>
                SVG
            ],
            'line' => [
                (new Svg())->addLine(5, 10, 15, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_override_default' => [
                (new Svg())->addLine(5, 10, 15, 20, ['stroke' => '#fff']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20" stroke="#fff" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_empty_attribute' => [
                (new Svg())->addLine(5, 10, 15, 20, ['test' => '']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20" test="" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_disable_default' => [
                (new Svg())->addLine(5, 10, 15, 20, ['stroke' => null]),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_without_defaults' => [
                (new Svg())->addLine(5, 10, 15, 20, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20"/>
                </svg>
                SVG
            ],
            'line_with_attributes' => [
                (new Svg())->addLine(5, 10, 15, 20, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <line x1="5" y1="10" x2="15" y2="20" test="123" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_in_group' => [
                (new Svg())
                    ->addGroup(['id' => 'test'])
                    ->getGroup('test')
                        ->addLine(5, 10, 15, 20, [], false)
                        ->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test">
                        <line x1="5" y1="10" x2="15" y2="20"/>
                    </g>
                </svg>
                SVG
            ],
            'circle' => [
                (new Svg())->addCircle(10, 20, 30),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="20" r="30" stroke="#000" stroke-width="1" fill="#000"/>
                </svg>
                SVG
            ],
            'circle_without_defaults' => [
                (new Svg())->addCircle(10, 20, 30, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="20" r="30"/>
                </svg>
                SVG
            ],
            'circle_with_attributes' => [
                (new Svg())->addCircle(10, 20, 30, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="20" r="30" test="123" stroke="#000" stroke-width="1" fill="#000"/>
                </svg>
                SVG
            ],
            'circle_with_title' => [
                (new Svg())->addCircle(10, 20, 30, ['title' => 'test']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="20" r="30" stroke="#000" stroke-width="1" fill="#000"><title>test</title></circle>
                </svg>
                SVG
            ],
            'circle_in_group' => [
                (new Svg())
                    ->addGroup(['id' => 'test'])
                    ->getGroup('test')
                        ->addCircle(10, 20, 30)
                        ->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test">
                        <circle cx="10" cy="20" r="30" stroke="#000" stroke-width="1" fill="#000"/>
                    </g>
                </svg>
                SVG
            ],
            'text' => [
                (new Svg())->addText('test', 10, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">test</text>
                </svg>
                SVG
            ],
            'text_with_special_characters' => [
                (new Svg())->addText('"\'<>&', 10, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">&quot;&apos;&lt;&gt;&amp;</text>
                </svg>
                SVG
            ],
            'text_without_defaults' => [
                (new Svg())->addText('test', 10, 20, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="20">test</text>
                </svg>
                SVG
            ],
            'text_with_attributes' => [
                (new Svg())->addText('test', 10, 20, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="20" test="123" font-family="sans-serif" font-size="12px" fill="#000">test</text>
                </svg>
                SVG
            ],
            'text_with_title' => [
                (new Svg())->addText('test', 10, 20, ['title' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">test<title>123</title></text>
                </svg>
                SVG
            ],
            'text_in_group' => [
                (new Svg())
                    ->addGroup(['id' => 'test'])
                    ->getGroup('test')
                    ->addText('test', 10, 20)
                    ->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test">
                        <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">test</text>
                    </g>
                </svg>
                SVG
            ],
            // TODO: add more path testcases
            'path' => [
                (new Svg())
                    ->addPath([
                        new MoveTo([['x' => 10, 'y' => 10]], true),
                        new LineTo([['x' => 20, 'y' => 20], ['x' => 30, 'y' => 30]], true),
                    ]),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <path d="M10,10 L20,20 30,30" fill="none" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'multiple' => [
                (new Svg())
                    ->addGroup(['id' => 'test1'])
                    ->getGroup('test1')
                        ->addCircle(10, 20, 30)
                        ->addText('test', 10, 20)
                        ->getParent()
                    ->addGroup(['id' => "test2"])
                    ->getGroup('test2')
                        ->addAttribute('test', '123')
                        ->addLine(10, 20, 30, 40)
                        ->addText('foo', 10, 20)
                        ->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                    <g id="test1">
                        <circle cx="10" cy="20" r="30" stroke="#000" stroke-width="1" fill="#000"/>
                        <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">test</text>
                    </g>
                    <g id="test2" test="123">
                        <line x1="10" y1="20" x2="30" y2="40" stroke="#000" stroke-width="1"/>
                        <text x="10" y="20" font-family="sans-serif" font-size="12px" fill="#000">foo</text>
                    </g>
                </svg>
                SVG
            ],
        ];
    }
}

