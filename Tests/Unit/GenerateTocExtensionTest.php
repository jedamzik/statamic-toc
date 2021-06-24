<?php

namespace Njed\Toc\Tests\Unit;

use Njed\Toc\Extensions\CommonMark\GenerateTocExtension;
use Njed\Toc\Tests\TestCase;
use Statamic\Facades\Markdown;

class GenerateTocExtensionTest extends TestCase
{
    public $content;

    public function setUp(): void
    {
        parent::setUp();

        Markdown::extend('default', function ($parser) {
            return $parser
                ->addExtension(function() { return new GenerateTocExtension; });
        });

        $this->content = <<<EOL
        # First Heading
        ## Second Heading
        ## Third Heading
        ### Fourth Heading
        ### Fifth Heading
        ## Sixth Heading
        #### Seventh Heading
        ##### Eight Heading
        ###### Ninth Heading

        Paragraph for testing
        
        * First list item
        * Second list item
        * Third list item
        EOL;
    }

    /** @test */
    public function it_returns_a_html_string()
    {
        $result = Markdown::parse($this->content);

        $this->assertIsString($result);
    }

    /** @test */
    public function it_strips_out_non_heading_elements()
    {
        $result = Markdown::parse($this->content);

        $this->assertStringNotContainsString('Paragraph for testing', $result);
        $this->assertStringNotContainsString('First list item', $result);
        $this->assertStringNotContainsString('Second list item', $result);
        $this->assertStringNotContainsString('Third list item', $result);
    }

    /** @test */
    public function it_returns_a_list_without_anchor_links_by_default()
    {
        $result = Markdown::parse($this->content);

        $this->assertStringNotContainsString('<a href', $result);
    }

    /** @test */
    public function it_returns_a_list_of_anchor_links_if_set_in_config()
    {
        config([
            'toc.anchorLinks' => true
        ]);

        $result = Markdown::parse($this->content);

        $this->assertStringContainsString('<a href', $result);
    }

    /** @test */
    public function it_only_returns_h1_and_h2_elements_by_default()
    {
        $result = Markdown::parse($this->content);

        $this->assertStringContainsString('First Heading', $result);
        $this->assertStringContainsString('Second Heading', $result);
        $this->assertStringContainsString('Third Heading', $result);
        $this->assertStringContainsString('Sixth Heading', $result);
        $this->assertStringNotContainsString('Fourth Heading', $result);
        $this->assertStringNotContainsString('Fifth Heading', $result);
        $this->assertStringNotContainsString('Seventh Heading', $result);
        $this->assertStringNotContainsString('Eight Heading', $result);
        $this->assertStringNotContainsString('Ninth Heading', $result);
    }

    /** @test */
    public function it_returns_additional_heading_elements_that_are_added_to_config()
    {
        config([
            'toc.includeLevels' => [3, 4]
        ]);

        $result = Markdown::parse($this->content);

        $this->assertStringContainsString('First Heading', $result);
        $this->assertStringContainsString('Second Heading', $result);
        $this->assertStringContainsString('Third Heading', $result);
        $this->assertStringContainsString('Sixth Heading', $result);
        $this->assertStringContainsString('Fourth Heading', $result);
        $this->assertStringContainsString('Fifth Heading', $result);
        $this->assertStringContainsString('Seventh Heading', $result);
        $this->assertStringNotContainsString('Eight Heading', $result);
        $this->assertStringNotContainsString('Ninth Heading', $result);
    }

    /** @test */
    public function it_adds_child_class_to_heading_elements_with_level_greater_than_two()
    {
        config([
            'toc.includeLevels' => [3, 4]
        ]);

        $result = Markdown::parse($this->content);
        $this->assertStringContainsString("<li class=\"child\">\n<p>Fourth Heading</p>\n</li>", $result);
        $this->assertStringContainsString("<li class=\"child\">\n<p>Fifth Heading</p>\n</li>", $result);
        $this->assertStringContainsString("<li class=\"child\">\n<p>Seventh Heading</p>\n</li>", $result);
    }

    /** @test */
    public function it_does_not_add_extension_if_there_are_no_headings()
    {
        $content = <<<EOL
        Paragraph for testing
        
        And a second paragraph
        EOL;

        $result = Markdown::parse($content);

        $this->assertStringNotContainsString('<ul', $result);
    }
}
