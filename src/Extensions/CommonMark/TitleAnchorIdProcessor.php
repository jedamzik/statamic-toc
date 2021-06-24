<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Block\Element\Heading;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;

final class TitleAnchorIdProcessor
{
    /** @var EnvironmentInterface  */
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

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
        $slug = str_slug($heading->getStringContent(), '-', config('app.locale', 'en'));

        $heading->data['attributes']['id'] = $slug;
    }
}
