<?php

namespace Njed\Toc\Tags;

use Statamic\Tags\Tags;

class Toc extends Tags
{
    /**
     * The {{ toc }} tag
     *
     * @return string|null
     */
    public function index(): ?string
    {
        if (!is_null($this->context->get('table_of_contents'))) {
            return view('statamic-toc::widget', [
                'table_of_contents' => $this->context->get('table_of_contents')
            ])->render();
        }

        return null;
    }
}
