<?php

namespace Njed\Toc\Tests\Unit;

use Njed\Toc\Listeners\GenerateToc;
use Njed\Toc\Tests\Factories\EntryFactory;
use Njed\Toc\Tests\TestCase;
use Statamic\Entries\Entry;
use Statamic\Events\Data\EntrySaving;

class GenerateTocListenerTest extends TestCase
{
    /** @test */
    public function it_should_handle_entry_saving_events()
    {
        $listener = $this->mock(GenerateToc::class);
        $this->app->instance(GenerateToc::class, $listener);
        $listener->shouldReceive('handle')->once();
        
        event(new EntrySaving(new Entry));
    }

    /** @test */
    public function it_should_not_generate_a_table_of_contents_if_entry_is_in_an_inactive_collection()
    {
        $entry = (new EntryFactory)->collection('pages')->make();
        $event = new EntrySaving($entry);

        (new GenerateToc)->handle($event);

        $this->assertNull($entry->value('table_of_contents'));
    }

    /** @test */
    public function it_should_save_the_generated_table_of_contents_in_an_entry()
    {
        config([
            'toc.collections' => [
                'pages' => 'content'
            ]
        ]);

        $content = <<<EOL
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

        $entry = (new EntryFactory)->collection('pages')->data([
            'content' => $content
        ])->make();
        $event = new EntrySaving($entry);

        (new GenerateToc)->handle($event);
        $this->assertNotNull($entry->value('table_of_contents'));
    }
}