<?php

namespace Concrete\Core\Entity\Express\Control;

use Concrete\Core\Express\Form\Control\TextEntityPropertyControlRenderer;
use Concrete\Core\Foundation\Environment;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetTextControls")
 */
class TextControl extends Control
{
    /**
     * @Column(type="text")
     */
    protected $text;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


    public function getFormRenderer()
    {
        return new TextEntityPropertyControlRenderer();
    }

}