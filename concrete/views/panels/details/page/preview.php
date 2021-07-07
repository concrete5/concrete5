<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
    $cp = new Permissions($c);
    if ($cp->canViewPageVersions()) {
        ?>

		<iframe id="ccm-page-preview-frame" name="ccm-page-preview-frame"></iframe>

        <div class="ccm-panel-detail-form-actions">
            <button class="float-end btn btn-success" type="button" data-panel-detail-action="submit"><?= t('Save Changes') ?></button>
        </div>

	<?php
    }
}
