<?php

namespace Njed\Toc\Extensions\CommonMark;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListData;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;

final class TransformHeadingsToListOfLinksProcessor
{
    public function __invoke(DocumentParsedEvent $e)
    {
        $walker = $e->getDocument()->walker();

        $data = new ListData;
        $data->markerOffset = 0;
        $data->type = ListBlock::TYPE_BULLET;

        $listBlock = new ListBlock($data);
        $listBlock->data->set('attributes', [
            'class' => 'table-of-contents',
        ]);

        while ($event = $walker->next()) {
            $node = $event->getNode();
            if ($event->isEntering() && $node instanceof Heading && ($node->getLevel() <= 2 || $this->configIncludesLevel($node->getLevel()))) {
                $content = $node->lastChild()->getLiteral();
                if (!config('toc.anchorLinks', false) ||
                    !$node->data->get('attributes') ||
                    !array_key_exists('id', $node->data->get('attributes')) ||
                    is_null($node->data->get('attributes')['id'])) {
                    $paragraph = new Paragraph;
                    $paragraph->appendChild(new Text($content));
                    $listItem = new ListItem($data);
                    $listItem->appendChild($paragraph);
                } else {
                    $fragment = $node->data->get('attributes')['id'];
                    $linkNode = new Link("#{$fragment}", $content, $content);

                    $paragraph = new Paragraph;
                    $paragraph->appendChild($linkNode);
                    $listItem = new ListItem($data);
                    $listItem->appendChild($paragraph);
                }

                $listItem->data->set('attributes', [
                    'class' => $this->getNodeClasses($node->getLevel())
                ]);

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
