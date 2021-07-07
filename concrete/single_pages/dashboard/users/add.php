<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Widget\FileFolderSelector;
use Concrete\Core\Support\Facade\Application;

/**
 * @var Concrete\Core\Permission\Access\ListItem\EditUserPropertiesUserListItem $assignment
 * @var Concrete\Controller\SinglePage\Dashboard\Users\Add $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 */
$app = Application::getFacadeApplication();
/** @var FileFolderSelector $fileFolderSelector */
$fileFolderSelector = $app->make(FileFolderSelector::class);

?>

<form method="post" action="<?= $view->action('submit'); ?>">
    <?= $form->getAutocompletionDisabler(); ?>
	<fieldset>
		<legend><?= t('Basic Details'); ?></legend>
		
		<div class="form-group">
            <?= $form->label('uName', t('Username')) ?>
            <div class="float-end">
            <span class="text-muted small">
                <?php echo t('Required') ?>
            </span>
            </div>
            <?= $form->text('uName', ['autofocus' => 'autofocus', 'autocomplete' => 'off']); ?>
		</div>

        <div class="form-group">
            <?= $form->label('uPassword', t('Password')) ?>
            <div class="float-end">
            <span class="text-muted small">
                <?php echo t('Required') ?>
            </span>
            </div>
            <?= $form->password('uPassword', ['autocomplete' => 'off']); ?>
		</div>

		<div class="form-group">
            <?= $form->label('uEmail', t('Email Address')) ?>
            <div class="float-end">
            <span class="text-muted small">
                <?php echo t('Required') ?>
            </span>
            </div>
            <?= $form->email('uEmail'); ?>
		</div>

		<?php if (count($locales)) { // "> 1" because en_US is always available?>
            <div class="form-group">
                <?= $form->label('uDefaultLanguage', t('Language')) ?>
                <div>
                    <?= $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
                </div>
            </div>
		<?php } ?>

        <div class="form-group">
            <?php echo $form->label('uHomeFileManagerFolderID', t('Home Folder')); ?>
            <?php echo $fileFolderSelector->selectFileFolder('uHomeFileManagerFolderID'); ?>
        </div>
	</fieldset>

<?php if (count($attribs) > 0) {
    ?>
	<fieldset>
		<legend><?=  t('Registration Data'); ?></legend>
        <?php
            foreach ($attribs as $ak) {
                if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
                    ?>
                <div class="form-group">
                    <?= $form->label('', $ak->getAttributeKeyDisplayName()) ?>
                    <div>
                        <?php $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), null, false); ?>
                    </div>
                </div>
                <?php
                }
            }
        ?>
	</fieldset>
<?php
} ?>
	<fieldset>
		<legend><?= t('Groups'); ?></legend>
		<div class="form-group">
            <?= $form->label('', t('Place this user into groups')) ?>
            <?php
                foreach ($gArray as $g) {
                    $gp = new Permissions($g);
                    if ($gp->canAssignGroup()) {
                    ?>
                    <div class="form-check">
                        <?= $form->checkbox('gID[]', $g->getGroupID(), isset($_POST['gID']) && is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])); ?>
                        <?= $form->label("gID_{$g->getGroupID()}", $g->getGroupDisplayName(), ['class' => 'form-check-label']); ?>
                    </div>
                    <?php
                    }
                }
            ?>
		</div>
    </fieldset>

	<?php $token->output('submit'); ?>

	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?= URL::to('/dashboard/users/search'); ?>" class="btn btn-secondary float-start"><?=  t('Cancel'); ?></a>
			<?= $form->submit('add', t('Add'), ['class' => 'btn btn-primary float-end']); ?>
		</div>
	</div>
</form>
