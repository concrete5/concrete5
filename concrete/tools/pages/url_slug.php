<?php

use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;

defined('C5_EXECUTE') or die('Access Denied.');
if (Core::make('helper/validation/token')->validate('get_url_slug', $_REQUEST['token'])) {
    $lang = Localization::activeLanguage();
    if (isset($_REQUEST['parentID']) && Core::make('multilingual/detector')->isEnabled()) {
        $ms = MultilingualSection::getBySectionOfSite(Page::getByID($_REQUEST['parentID']));
        if (is_object($ms)) {
            $lang = $ms->getLanguage();
        }
    }
    $text = Core::make('helper/text');
    $name = $text->urlify($_REQUEST['name'], Config::get('concrete.seo.segment_max_length'), $lang);

    echo $name;
}
