<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Block\Block;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Concrete\Core\Permission\Category;

/** @var Block $b */
/** @var int $rcID */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

$c = $b->getBlockCollectionObject();
$arHandle = $b->getAreaHandle();

$pk = Key::getByHandle('view_block');
$pk->setPermissionObject($b);
$list = $pk->getAccessListItems();

foreach ($list as $pa) {
    $pae = $pa->getAccessEntityObject();

    if ($pae->getAccessEntityTypeHandle() == 'group') {
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $groupObject = $pae->getGroupObject();

        if ($groupObject instanceof Group) {
            if ($groupObject->getGroupID() == GUEST_GROUP_ID) {
                $pd = $pa->getPermissionDurationObject();

                if (!is_object($pd)) {
                    $pd = new Duration();
                }
            }
        }
    }
}
?>

<div class="ccm-ui" id="ccm-permissions-access-entity-wrapper">
    <!--suppress HtmlUnknownTarget -->
    <form id="ccm-permissions-timed-guest-access-form" class="form-stacked" method="post"
          action="<?= h(Category::getByHandle('block')->getTaskURL()) ?>">

        <?php echo $form->hidden('task', 'set_timed_guest_access'); ?>
        <?php echo $form->hidden('cID', $c->getCollectionID()); ?>
        <?php echo $form->hidden('bID', $b->getBlockID()); ?>
        <?php echo $form->hidden('arHandle', $arHandle); ?>

        <p>
            <?php echo t('When should guests be able to view this block?') ?>
        </p>

        <?php /** @noinspection PhpUnhandledExceptionInspection */
        echo View::element('permission/duration', ['pd' => $pd]); ?>

        <div class="dialog-buttons">
            <input type="button" onclick="jQuery.fn.dialog.closeTop()" value="<?php echo h(t('Cancel')) ?>"
                   class="btn btn-secondary float-start"/>

            <input type="submit" onclick="$('#ccm-permissions-timed-guest-access-form').submit()"
                   value="<?php echo h(t('Save')) ?>" class="btn btn-primary float-end"/>
        </div>
    </form>
</div>

<!--suppress JSUnresolvedVariable -->
<script type="text/javascript">
    $(function () {
        $("#ccm-permissions-timed-guest-access-form").ajaxForm({
            beforeSubmit: function () {
                jQuery.fn.dialog.showLoader();
            },
            success: function () {
                ConcreteToolbar.disableDirectExit();
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.closeTop();
                ConcreteAlert.notify({
                    'message': ccmi18n.scheduleGuestAccessSuccess,
                    'title': ccmi18n.scheduleGuestAccess
                });
            }
        });
    });
</script>
