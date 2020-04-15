<?php

namespace Njed\Toc\Listeners;

use Njed\Toc\Extensions\CommonMark\GenerateTocExtension;
use Statamic\Events\Data\EntrySaving;
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
        if (collect(config('toc.collections'))->keys()->contains($event->data->collectionHandle())) {
            $content = $event->data->value(config("toc.collections.{$event->data->collectionHandle()}", 'content'));

            Markdown::extend('default', function ($parser) {
                return $parser
                    ->addExtension(fn() => new GenerateTocExtension);
            });

            $headings = Markdown::parse($content);

            $event->data->set('table_of_contents', trim($headings));
        }
    }
}
