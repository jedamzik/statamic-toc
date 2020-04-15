<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

final class TransformHeadingsToListOfLinksProcessor
{
    private EnvironmentInterface $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        $data = new ListData;
        $data->markerOffset = 0;
        $data->type = ListBlock::TYPE_UNORDERED;

        $listBlock = new ListBlock($data);
        $listBlock->data['attributes']['class'] = 'table-of-contents';

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($event->isEntering() && $node instanceof Heading && ($node->getLevel() <= 2 || $this->configIncludesLevel($node->getLevel()))) {
                if (!config('toc.anchorLinks', false) ||
                    !$node->getData('attributes') ||
                    !array_key_exists('id', $node->getData('attributes')) ||
                    is_null($node->data['attributes']['id'])) {
                    $paragraph = new Paragraph;
                    $paragraph->appendChild(new Text($node->getStringContent()));
                    $listItem = new ListItem($data);
                    $listItem->appendChild($paragraph);
                } else {
                    $fragment = $node->getData('attributes')['id'];
                    $linkNode = new Link("#{$fragment}", $node->getStringContent(), $node->getStringContent());

                    $paragraph = new Paragraph;
                    $paragraph->appendChild($linkNode);
                    $listItem = new ListItem($data);
                    $listItem->appendChild($paragraph);
                }


                if ($node->getLevel() > 2) {
                    $listItem->data['attributes']['class'] = 'child';
                }

                $listBlock->appendChild($listItem);
            }
        }

        $e->getDocument()->detachChildren();
        $e->getDocument()->appendChild($listBlock);
    }

    private function configIncludesLevel(string $level): bool
    {
        if (is_null(config('toc.includeLevels'))) {
            return false;
        }

        return collect(config('toc.includeLevels'))->contains($level);
    }
}