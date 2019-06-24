<?php
defined('C5_EXECUTE') or die('Access Denied.');

echo $user_selector->selectUser($this->field('value'), $value);
