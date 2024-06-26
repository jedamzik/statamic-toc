<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Event\DocumentParsedEvent;
use Illuminate\Support\Str;

final class TitleAnchorIdProcessor
{
    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($event->isEntering() && $node instanceof Heading) {
                self::addAnchorIdToHeading($node);
            }
        }
    }

    private static function addAnchorIdToHeading(Heading $heading): void
    {
        $slug = Str::slug($heading->lastChild()->getLiteral(), '-', config('app.locale', 'en'));

        $heading->data->set('attributes', [
            'id' => $slug
        ]);
    }
}
