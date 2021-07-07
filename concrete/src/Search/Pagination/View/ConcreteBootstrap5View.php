<?php
namespace Concrete\Core\Search\Pagination\View;

use Pagerfanta\View\TwitterBootstrap5View;

class ConcreteBootstrap5View extends TwitterBootstrap5View implements ViewInterface
{
    protected function createDefaultTemplate()
    {
        return new ConcreteBootstrap5Template();
    }

    public function getArguments()
    {
        return array(
            'prev_message' => tc('Pagination', '&larr; Previous'),
            'next_message' => tc('Pagination', 'Next &rarr;'),
            'active_suffix' => '<span class="visually-hidden">' . tc('Pagination', '(current)') . '</span>',
        );
    }
}
