<?php
defined('C5_EXECUTE') or die('Access Denied.');
/* @var Concrete\Core\Block\View\BlockView $view */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var Concrete\Core\Entity\Notification\NotificationAlert[] $items */
/* @var array $filterValues */
/* @var string $filter */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Search\Pagination\Pagination $pagination */
?>
<div class="ccm-block-desktop-waiting-for-me" data-wrapper="desktop-waiting-for-me">


    <div data-list="notification">

        <h3><?=t('Waiting For Me')?>
            <i class="ccm-block-desktop-waiting-for-me-loader fa fa-refresh fa-spin"></i>
        </h3>


        <div data-form="notification">
            <form method="get" action="<?=$view->action('reload_results')?>">
            <div class="form-group" style="font-size: 12px">
                <?=$form->select('filter', $filterValues, h($filter))?>
            </div>
            </form>
        </div>

        <?php

        foreach($items as $item) {
            $notification = $item->getNotification();
            /**
             * @var $listView \Concrete\Core\Notification\View\ListViewInterface
             */
            $listView = $notification->getListView();
            $action = $listView->getFormAction();

            ?>

        <div class="ccm-block-desktop-waiting-for-me-item" data-notification-alert-id="<?=$item->getNotificationAlertID()?>"
        data-token="<?=$token->generate()?>">
            <?php if ($action) { ?>
                <form action="<?=$action?>" method="post">
            <?php }  ?>

                <div class="ccm-block-desktop-waiting-for-me-icon">
                    <?php print $listView->renderIcon() ?>
                </div>

                <div class="ccm-block-desktop-waiting-for-me-details">
                    <?php print $listView->renderDetails() ?>
                </div>

                <div class="ccm-block-desktop-waiting-for-me-menu">
                    <?php print $listView->renderMenu() ?>
                </div>


            <?php if ($action) { ?>
                </form>
            <?php }  ?>

        </div>

            <?php
        }

        ?>

        <p <?php if (count($items)) { ?> style="display: none"<?php }  ?> data-notification-description="empty"><?=t('There are no items that currently need your attention.')?></p>

        <?php if ($pagination && $pagination->haveToPaginate()) {
            $pagination->setBaseURL($view->action('reload_results') . '?filter=' . rawurlencode($filter));
            ?>

            <?=$pagination->renderDefaultView(); ?>

        <?php } ?>

    </div>



    <script type="text/javascript">
        $(function() {
            $('div[data-list=notification]').concreteNotificationList();
        });
    </script>

</div>
