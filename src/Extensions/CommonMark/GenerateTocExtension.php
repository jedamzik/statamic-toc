<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;

final class GenerateTocExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addEventListener(DocumentParsedEvent::class, new TitleAnchorIdProcessor($environment));
        $environment->addEventListener(DocumentParsedEvent::class, new TransformHeadingsToListOfLinksProcessor($environment));
    }
}