<?php

use Concrete\Core\Permission\Access\Entity\Type as PermissionAccessEntityType;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Permission\Category|null $pkCategory
 * @var string $accessType
 * @var Concrete\Core\Permission\Access\Entity\Entity|null $pae
 * @var bool $disableDuration
 * @var Concrete\Core\Permission\Duration|null $pd
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Form\Service\Form $form
 */
?>
<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">
    <form id="ccm-permissions-access-entity-form" method="POST" action="<?= h($resolverManager->resolve(['/ccm/system/permissions/access/entity/save'])) ?>">
        <input type="hidden" name="task" value="save_permissions" />
        <?= $form->hidden('accessType') ?>
        <?= $form->hidden('peID') ?>
        <?= $form->hidden('pdID') ?>
        <h4><?= t('Access') ?></h4>
        <p><?= t('Who gets access to this permission?') ?></p>
        <div id="ccm-permissions-access-entity-label">
            <?php
            if ($pae !== null) {
                ?><div class="alert alert-info"><?= $pae->getAccessEntityLabel() ?></div><?php
            } else {
                ?><div class="alert alert-warning"><?= t('None Selected') ?></div><?php
            }
            ?>
        </div>
        <?php
        if ($pae === null) {
            $entitytypes = PermissionAccessEntityType::getList($pkCategory);
            ?>
            <div class="btn-group">
                <a class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <i class="fas fa-plus"></i> <?= t('Select') ?> <span class="caret"></span>
                </a>
                <div class="dropdown-menu">
                    <?php
                    foreach ($entitytypes as $type) {
                        echo $type->getAccessEntityTypeLinkHTML();
                    }
                    ?>
                </div>
            </div>
            <br />
            <br />
            <?php
            foreach ($entitytypes as $type) {
                View::element(
                    "permission/access/entity/types/{$type->getAccessEntityTypeHandle()}",
                    ['type' => $type],
                    $type->getPackageID() > 0 ? $type->getPackageHandle() : null
                );
            }
        }
        if ($disableDuration === false) {
            ?>
            <h4><?= t('Time Settings') ?></h4>
            <?php
            View::element('permission/duration', ['pd' => $pd]);
        }
        ?>
        <div class="dialog-buttons">
            <input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?= t('Cancel') ?>" class="btn btn-secondary" />
            <input type="submit" onclick="$('#ccm-permissions-access-entity-form').submit()" value="<?= t('Save') ?>" class="btn btn-primary ms-auto" />
        </div>
    </form>
</div>
<script>
$(document).ready(function() {
    $("#ccm-permissions-access-entity-form").ajaxForm({
        beforeSubmit: function(r) {
            $.fn.dialog.showLoader();
        },
        error: function(xhr) {
            $.fn.dialog.hideLoader();
            ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.renderErrorResponse(xhr, true))
        },
        success: function(r) {
            $.fn.dialog.hideLoader();
            if (typeof(ccm_addAccessEntity) == 'function') {
                ccm_addAccessEntity(r.peID, r.pdID, <?= json_encode(h($accessType)) ?>);
            } else {
                alert(r.peID);
                alert(r.pdID);
            }
        }
    });
});
</script>
