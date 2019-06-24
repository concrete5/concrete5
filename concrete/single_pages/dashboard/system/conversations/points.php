<?php defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$token = Core::make('token');
?>
<h4><?=t('Installed Rating Types')?></h4>
<?php if (count($ratingTypes) > 0) {
    ?>
    <form action="<?=$view->action('save')?>" method="post">
        <?php $token->output('conversation_points') ?>
        <table class="table">
            <thead>
            <tr>
                <th><?=t('Name')?></th>
                <th><?=t('Point Value')?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($ratingTypes as $ratingType) {
    ?>
                <tr>
                    <td><?=$ratingType->getConversationRatingTypeDisplayName();
    ?></td>
                    <td><?=$form->number('rtPoints_' . $ratingType->getConversationRatingTypeID(), $ratingType->cnvRatingTypeCommunityPoints, array('style' => 'width: 100px'))?></td>
                </tr>
            <?php 
}
    ?>
            </tbody>
        </table>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?=$form->submit('save', t('Save'), array(), 'btn-primary pull-right')?>
            </div>
        </div>
    </form>
<?php 
} else {
    ?>
    <p><?=t('There are no Community Points Rating Types installed.')?></p>
<?php 
} ?>
