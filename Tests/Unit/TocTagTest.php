<?php

namespace Njed\Toc\Tests\Unit;

use Njed\Toc\Tags\Toc;
use Njed\Toc\Tests\TestCase;
use Statamic\View\Antlers\Parser;

class TagTest extends TestCase
{
    public $tag;
    public $context;

    public function setUp(): void
    {
        parent::setUp();

        $this->tag = new Toc();
        $this->tag->setParser(new Parser);
        $this->tag->setContext([]);
    }

    /** @test */
    public function it_has_been_registered()
    {
        $this->assertTrue(isset(app('statamic.tags')['toc']));
    }

    /** @test */
    public function it_returns_null_if_no_data_is_set_in_context()
    {
        $this->assertNull($this->tag->index());
    }

    /** @test */
    public function it_returns_data_in_rendered_view_string()
    {
        $data = '<p>test</p>';

        $this->tag->setContext([
            'table_of_contents' => $data
        ]);

        $this->assertIsString($this->tag->index());
        $this->assertStringContainsString($data, $this->tag->index());
    }

    /** @test */
    public function it_uses_the_view_template_to_render_output()
    {
        $data = '<p>content</p>';
        $view = view('statamic-toc::widget', ['table_of_contents' => $data]);

        $this->tag->setContext([
            'table_of_contents' => $data
        ]);

        $this->assertIsString($this->tag->index());
        $this->assertEquals($view->render(), $this->tag->index());
    }
}