<?php
namespace Nerdman\Svg;

use PHPStan\Testing\TestCase;

class SvgTest extends TestCase
{
    /**
     * @param Svg $svg
     * @param string $expected
     *
     * @dataProvider \Nerdman\Svg\SvgTest::provider()
     */
    public function testSvg(Svg $svg, string $expected)
    {
        $this->assertEquals($expected, $svg->__toString());
    }

    public function provider(): array
    {
        return [
            'empty' => [
                new Svg(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                </svg>
                SVG
            ],
            'viewbox' => [
                (new Svg())->addViewBox(0, 0, 100, 100),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                </svg>
                SVG
            ],
            'dimensions' => [
                (new Svg())->addDimensions(100, 200),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="200">
                </svg>
                SVG
            ],
            'namespace' => [
                (new Svg())->addNamespace('test'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns="test">
                </svg>
                SVG
            ],
            'attribute' => [
                (new Svg())->addAttribute('test', '123'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" test="123">
                </svg>
                SVG
            ],
            'attribute_with_special_character' => [
                (new Svg())->addAttribute('test', '"\'<>&'),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg" test="&quot;&apos;&lt;&gt;&amp;">
                </svg>
                SVG
            ],
            'group' => [
                (new Svg())->addGroup([]),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <g>
                </g>
                </svg>
                SVG
            ],
            'group_with_id' => [
                (new Svg())->addGroup(['id' => 'test', 'another' => 'testtest']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <g id="test" another="testtest">
                </g>
                </svg>
                SVG
            ],
            'group_in_group' => [
                (new Svg())->addGroup(['id' => 'test'])->getGroup('test')->addGroup([])->getParent(),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <g id="test">
                <g>
                </g>
                </g>
                </svg>
                SVG
            ],
            'identifiable_group' => [
                (new Svg())->addGroup(['id' => 'test'])->getGroup('test'),
                <<<'SVG'
                <g id="test">
                </g>
                SVG
            ],
            'line' => [
                (new Svg())->addLine(5, 10, 15, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_override_default' => [
                (new Svg())->addLine(5, 10, 15, 20, ['stroke' => '#fff']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000" stroke="#fff" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_empty_attribute' => [
                (new Svg())->addLine(5, 10, 15, 20, ['test' => '']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000" test="" stroke="#000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_disable_default' => [
                (new Svg())->addLine(5, 10, 15, 20, ['stroke' => null]),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000" stroke-width="1"/>
                </svg>
                SVG
            ],
            'line_without_defaults' => [
                (new Svg())->addLine(5, 10, 15, 20, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000"/>
                </svg>
                SVG
            ],
            'line_with_attributes' => [
                (new Svg())->addLine(5, 10, 15, 20, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000" test="123" stroke="#000" stroke-width="1"/>
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
                <line x1="5.000000" y1="10.000000" x2="15.000000" y2="20.000000"/>
                </g>
                </svg>
                SVG
            ],
            'circle' => [
                (new Svg())->addCircle(10, 20, 30),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <circle cx="10.000000" cy="20.000000" r="30.000000" stroke-color="#000000" stroke-width="1" fill="#000000"/>
                </svg>
                SVG
            ],
            'circle_without_defaults' => [
                (new Svg())->addCircle(10, 20, 30, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <circle cx="10.000000" cy="20.000000" r="30.000000"/>
                </svg>
                SVG
            ],
            'circle_with_attributes' => [
                (new Svg())->addCircle(10, 20, 30, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <circle cx="10.000000" cy="20.000000" r="30.000000" test="123" stroke-color="#000000" stroke-width="1" fill="#000000"/>
                </svg>
                SVG
            ],
            'circle_with_title' => [
                (new Svg())->addCircle(10, 20, 30, ['title' => 'test']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <circle cx="10.000000" cy="20.000000" r="30.000000" stroke-color="#000000" stroke-width="1" fill="#000000"><title>test</title></circle>
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
                <circle cx="10.000000" cy="20.000000" r="30.000000" stroke-color="#000000" stroke-width="1" fill="#000000"/>
                </g>
                </svg>
                SVG
            ],
            'text' => [
                (new Svg())->addText('test', 10, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">test</text>
                </svg>
                SVG
            ],
            'text_with_special_characters' => [
                (new Svg())->addText('"\'<>&', 10, 20),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">&quot;&apos;&lt;&gt;&amp;</text>
                </svg>
                SVG
            ],
            'text_without_defaults' => [
                (new Svg())->addText('test', 10, 20, [], false),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <text x="10.000000" y="20.000000">test</text>
                </svg>
                SVG
            ],
            'text_with_attributes' => [
                (new Svg())->addText('test', 10, 20, ['test' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <text x="10.000000" y="20.000000" test="123" font-family="sans-serif" font-size="12px" fill="#000000">test</text>
                </svg>
                SVG
            ],
            'text_with_title' => [
                (new Svg())->addText('test', 10, 20, ['title' => '123']),
                <<<'SVG'
                <?xml version="1.0" encoding="utf-8"?>
                <svg xmlns="http://www.w3.org/2000/svg">
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">test<title>123</title></text>
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
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">test</text>
                </g>
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
                <circle cx="10.000000" cy="20.000000" r="30.000000" stroke-color="#000000" stroke-width="1" fill="#000000"/>
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">test</text>
                </g>
                <g id="test2" test="123">
                <line x1="10.000000" y1="20.000000" x2="30.000000" y2="40.000000" stroke="#000" stroke-width="1"/>
                <text x="10.000000" y="20.000000" font-family="sans-serif" font-size="12px" fill="#000000">foo</text>
                </g>
                </svg>
                SVG
            ],
        ];
    }
}

