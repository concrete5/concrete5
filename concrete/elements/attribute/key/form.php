<?php

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Attribute\StandardSetManager;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Url\UrlImmutable $action
 * @var Concrete\Core\Url\UrlImmutable|null $back
 * @var Concrete\Core\Attribute\Category\CategoryInterface|null $category
 * @var Concrete\Core\Attribute\AttributeKeyInterface|null $key
 * @var Concrete\Core\Entity\Attribute\Type $type
 * @var string|null $akHandle
 */

$app = Application::getFacadeApplication();

$form = $app->make('helper/form');
$valt = $app->make('helper/validation/token');

if (!isset($category) || !$category instanceof CategoryInterface) {
    $category = null;
}
if (!isset($key) || !$key instanceof AttributeKeyInterface) {
    $key = null;
}
if (!isset($akHandle)) {
    $akHandle = $key === null ? '' : $key->getAttributeKeyHandle();
}
if (!isset($back)) {
    $back = $app->make(ResolverManagerInterface::class)->resolve([Page::getCurrentPage()]);
}

$asID = 0;
if ($key !== null) {
    $currentSets = $app->make(SetFactory::class)->getByAttributeKey($key);
    if (count($currentSets) === 1) {
        $asID = $currentSets[0]->getAttributeSetID();
    }
}
?>


<form method="post" action="<?= $action ?>" id="ccm-attribute-key-form">
    <?php
    echo $form->hidden('atID', $type->getAttributeTypeID());
    if ($key !== null) {
        echo $form->hidden('akID', $key->getAttributeKeyID());
    }
    ?>
    <fieldset>

        <legend><?= t('%s: Basic Details', $type->getAttributeTypeDisplayName()) ?></legend>

        <div class="form-group">
            <?= $form->label('akHandle', t('Handle')) ?>
            <div class="input-group">
                <?= $form->text('akHandle', $akHandle, ['autofocus' => 'autofocus']) ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <div class="form-group">
            <?= $form->label('akName', t('Name')) ?>
            <div class="input-group">
                <?= $form->text('akName', $key === null ? '' : $key->getAttributeKeyName()) ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>

        <?php
        if ($category !== null && $category->getSetManager()->allowAttributeSets() == StandardSetManager::ASET_ALLOW_SINGLE) {
            ?>
            <div class="form-group">
                <?= $form->label('asID', t('Set')) ?>
                <div class="controls">
                    <?php
                        $sel = ['0' => t('** None')];
                        $sets = $category->getSetManager()->getAttributeSets();
                        foreach ($sets as $as) {
                            $sel[$as->getAttributeSetID()] = $as->getAttributeSetDisplayName();
                        }
                        echo $form->select('asID', $sel, $asID);
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

        <div class="form-group">
            <label class="control-label form-label"><?= t('Searchable') ?></label>
            <div class="form-check">
                <?= $form->checkbox('akIsSearchableIndexed', 1, $key !== null && $key->isAttributeKeyContentIndexed(), ['class' => 'form-check-input']) ?>
                <label class="form-check-label" for="akIsSearchableIndexed">
                    <?= t('Content included in search index.') ?>
                </label>
            </div>
            <div class="form-check">
                <?= $form->checkbox('akIsSearchable', 1, $key === null || $key->isAttributeKeySearchable(), ['class' => 'form-check-input']) ?>
                <label class="form-check-label" for="akIsSearchable">
                    <?= t('Field available in advanced search.') ?>
                </label>
                <?php
                if ($key && $key->isAttributeKeySearchable()) {
                    ?>
                <div class="alert alert-danger small d-none" id="akIsSearchable-warning">
                    <?= t(
                        'WARNING: you will need to re-run the %s automated job if you uncheck this value, save the attribute, and then re-check this value',
                        '<strong>' . t('Index Search Engine - All') . '</strong>'
                    ) ?>
                </div>
                <script>
                $(document).ready(function() {
                    $('#akIsSearchable')
                        .on('change', function() {
                            $('#akIsSearchable-warning').toggleClass('d-none', $(this).is(':checked'))
                        })
                        .trigger('change')
                    ;
                });
                </script>
                <?php
                }
            ?>
            </div>
        </div>
    </fieldset>

<?php
    if ($category && $category instanceof \Concrete\Core\Attribute\Category\StandardCategoryInterface &&
        !$category instanceof \Concrete\Core\Attribute\Category\ExpressCategory) {

        echo $form->hidden('akCategoryID', $category->getCategoryEntity()->getAttributeKeyCategoryID());

        try {
            View::element(
                'attribute/categories/' . $category->getCategoryEntity()->getAttributeKeyCategoryHandle(),
                ['key' => $key],
                $category->getCategoryEntity()->getPackageID() ? $category->getCategoryEntity()->getPackageHandle() : null
            );
        } catch (ErrorException $e) {
            if (strpos($e->getMessage(), 'No such file or directory') === false) {
                // This isn't the error we expected to catch
                throw $e;
            }
        }
    }

    $valt->output('add_or_update_attribute');
    $type->render(new \Concrete\Core\Attribute\Context\AttributeTypeSettingsContext(), $key ?? null);
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $back ?>" class="btn float-start btn-secondary"><?= t('Back') ?></a>
            <button type="submit" class="btn btn-primary float-end"><?= $key === null ? t('Add') : t('Save') ?></button>
        </div>
    </div>

</form>
