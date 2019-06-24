<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    print $form->number(
        $this->field('value'),
        $value,
        [
            'step' => 'any'
        ]
    );
