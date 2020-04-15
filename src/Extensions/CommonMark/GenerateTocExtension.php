<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;

final class GenerateTocExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment->addEventListener(DocumentParsedEvent::class, new TitleAnchorIdProcessor($environment));
        $environment->addEventListener(DocumentParsedEvent::class, new TransformHeadingsToListOfLinksProcessor($environment));
    }
}