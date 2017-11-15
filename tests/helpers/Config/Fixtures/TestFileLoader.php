<?php

namespace Concrete\TestHelpers\Config\Fixtures;

use Concrete\Core\Config\FileLoader;

class TestFileLoader extends FileLoader
{
    /**
     * TestFileLoader constructor.
     *
     * @param mixed $files
     */
    public function __construct($files)
    {
        parent::__construct($files);
        $this->defaultPath = DIR_TESTS . '/config';
    }
}
