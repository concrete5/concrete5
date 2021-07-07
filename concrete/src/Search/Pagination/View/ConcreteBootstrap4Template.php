<?php
namespace Concrete\Core\Search\Pagination\View;

use Pagerfanta\View\Template\TwitterBootstrap4Template;

class ConcreteBootstrap4Template extends TwitterBootstrap4Template
{

    public function container(): string
    {
        return sprintf('<div class="ccm-pagination-wrapper"><ul class="%s">%%pages%%</ul></div>',
            $this->option('css_container_class')
        );
    }

}
