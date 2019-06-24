<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    print $form->url(
        $this->field('value'),
        $value
    );
