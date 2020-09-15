<?php

namespace Njed\Toc\Listeners;

use Njed\Toc\Extensions\CommonMark\GenerateTocExtension;
use Statamic\Events\EntrySaving;
use Statamic\Facades\Markdown;

class GenerateToc
{
    /**
     * Handle the event.
     *
     * @param  EntrySaving  $event
     * @return void
     */
    public function handle(EntrySaving $event)
    {
        if (collect(config('toc.collections'))->keys()->contains($event->entry->collectionHandle())) {
            $content = $event->entry->value(config("toc.collections.{$event->entry->collectionHandle()}", 'content'));

            Markdown::extend('default', function ($parser) {
                return $parser
                    ->addExtension(fn() => new GenerateTocExtension);
            });

            $headings = Markdown::parse($content);

            $event->entry->set('table_of_contents', trim($headings));
        }
    }
}
