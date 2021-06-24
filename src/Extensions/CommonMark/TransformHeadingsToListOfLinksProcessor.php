<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

final class TransformHeadingsToListOfLinksProcessor
{
    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        $data = new ListData;
        $data->markerOffset = 0;
        $data->type = ListBlock::TYPE_BULLET;

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

                $listItem->data['attributes']['class'] = $this->getNodeClasses($node->getLevel());

                $listBlock->appendChild($listItem);
            }
        }

        $e->getDocument()->detachChildren();
        // Only if there are items should we add the TOC
        if (!empty($listBlock->children())) {
            $e->getDocument()->appendChild($listBlock);
        }
    }

    private function configIncludesLevel(string $level): bool
    {
        if (is_null(config('toc.includeLevels'))) {
            return false;
        }

        return collect(config('toc.includeLevels'))->contains($level);
    }

    /**
     * Returns the classes for a list element based on it's depth
     *
     * @param $nodeLevel
     * @return string
     */
    private function getNodeClasses($nodeLevel): string
    {
        $classes = [];
        // h3 headings
        if ($nodeLevel > 2) {
            $classes[] = 'child';
        }
        // h4 headings
        if ($nodeLevel > 3) {
            $classes[] = 'grandchild';
        }
        return implode(' ', $classes);
    }
}
