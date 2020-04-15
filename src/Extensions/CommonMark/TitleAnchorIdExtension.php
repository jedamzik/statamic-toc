<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use Njed\Toc\Extensions\CommonMark\TitleAnchorIdProcessor;

final class TitleAnchorIdExtension implements ExtensionInterface
{
    public function register(ConfigurableEnvironmentInterface $environment)
    {
        $environment->addEventListener(DocumentParsedEvent::class, new TitleAnchorIdProcessor($environment));
    }
}