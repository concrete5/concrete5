<?php
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Page\Type\Composer\OutputControl as PageTypeComposerOutputControl;
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;

$c = Page::getCurrentPage();
    // retrieve all block controls attached to this page template.
    $pt = PageTemplate::getByID($c->getPageTemplateID());
    $ptt = PageType::getByDefaultsPage($c);
    $controls = PageTypeComposerOutputControl::getList($ptt, $pt);
    $values = array();
    foreach ($controls as $control) {
        $fls = PageTypeComposerFormLayoutSetControl::getByID($control->getPageTypeComposerFormLayoutSetControlID());
        if ($fls->getPageTypeComposerFormLayoutSetControlCustomLabel()) {
            $displayname = $fls->getPageTypeComposerFormLayoutSetControlCustomLabel();
        } else {
            $cc = $fls->getPageTypeComposerControlObject();
            $displayname = $cc->getPageTypeComposerControlDisplayName();
        }
        $values[$control->getPageTypeComposerOutputControlID()] = $displayname;
    }
    $form = Loader::helper('form');
?>
<div class="form-group">
	<label for="ptComposerOutputControlID" class="control-label form-label"><?=t('Control')?></label>
	<?=$form->select('ptComposerOutputControlID', $values, $ptComposerOutputControlID)?>
</div>
