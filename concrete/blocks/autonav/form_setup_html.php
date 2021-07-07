<?php
defined('C5_EXECUTE') or die("Access Denied.");

/* @var Concrete\Block\Autonav\Controller $controller */
/* @var Concrete\Core\Block\View\BlockView $view */

/* @var Concrete\Core\Entity\Block\BlockType\BlockType $bt */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Validation\CSRF\Token $validation_token */
/* @var array $info */

$c = Page::getCurrentPage();

if (!isset($info)) {
    $info = [];
}

$info += [
    'orderBy' => null,
    'displayUnavailablePages' => null,
    'displayPages' => null,
    'displayPagesCID' => null,
    'displaySubPages' => null,
    'displaySubPageLevels' => null,
    'displaySubPageLevelsNum' => null,
];
?>

<?=$concrete_ui->tabs(array(
    array('autonav-settings', t('Settings'), true),
    array('autonav-preview', t('Preview'))
)); ?>

<div class="tab-content">
    <div class="tab-pane show active" id="autonav-settings">
        <div class="autonav-form">
            <input type="hidden" name="autonavCurrentCID" value="<?= $c->getCollectionID() ?>"/>
            <input type="hidden" name="autonavPreviewPane" value="<?= h($controller->getActionURL('preview_pane')) ?>"/>
            <input type="hidden" name="autonavPreviewPaneTokenName" value="<?= h($validation_token::DEFAULT_TOKEN_NAME) ?>" />
            <input type="hidden" name="autonavPreviewPaneTokenValue" value="<?= h($validation_token->generate('ccm-autonav-preview')) ?>" />

            <fieldset>
                <div class="form-group">
                    <label for="orderBy"><?= t('Page Order') ?></label>
                    <select class="form-select" name="orderBy">
                        <?php
                        $order = $info['orderBy'];
                        ?>
                        <option value="display_asc" <?= $order === 'display_asc' ? 'selected' : '' ?>>
                            <?= t('in their sitemap order.') ?>
                        </option>
                        <option value="chrono_desc" <?= $order === 'chrono_desc' ? 'selected' : '' ?>>
                            <?= t('with the most recent first.') ?>
                        </option>
                        <option value="chrono_asc" <?= $order === 'chrono_asc' ? 'selected' : '' ?>>
                            <?= t('with the earliest first.') ?>
                        </option>
                        <option value="alpha_asc" <?= $order === 'alpha_asc' ? 'selected' : '' ?>>
                            <?= t('in alphabetical order.') ?>
                        </option>
                        <option value="alpha_desc" <?= $order === 'alpha_desc' ? 'selected' : '' ?>>
                            <?= t('in reverse alphabetical order.') ?>
                        </option>
                        <option value="display_desc" <?= $order === 'display_desc' ? 'selected' : '' ?>>
                            <?= t('in reverse sitemap order.') ?>
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= t('Check Page Permissions') ?></label>
                    <div class="form-check">
                        <?= $form->checkbox('displayUnavailablePages', 1, $info['displayUnavailablePages']); ?>
                        <label for="displayUnavailablePages" class="form-check-label"><?= t('Display links that may require login.'); ?></label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="displayPages"><?= t('Begin Auto Nav') ?></label>
                    <select name="displayPages" onchange="toggleCustomPage(this.value);" class="form-select">
                        <option value="top"<?php if ($info['displayPages'] == 'top') {
                            ?> selected<?php } ?>>
                            <?= t('at the top level'); ?>
                        </option>
                        <option value="second_level"<?php if ($info['displayPages'] == 'second_level') {
                            ?> selected<?php } ?>>
                            <?= t('at the second level') ?>
                        </option>
                        <option value="third_level"<?php if ($info['displayPages'] == 'third_level') {
                            ?> selected<?php } ?>>
                            <?= t('at the third level') ?>
                        </option>
                        <option value="above"<?php if ($info['displayPages'] == 'above') {
                            ?> selected<?php } ?>>
                            <?= t('at the level above') ?>
                        </option>
                        <option value="current"<?php if ($info['displayPages'] == 'current') {
                            ?> selected<?php } ?>>
                            <?= t('at the current level') ?>
                        </option>
                        <option value="below"<?php if ($info['displayPages'] == 'below') {
                            ?> selected<?php } ?>>
                            <?= t('At the level below') ?>
                        </option>
                        <option value="custom"<?php if ($info['displayPages'] == 'custom') {
                            ?> selected<?php } ?>>
                            <?= t('Beneath a particular page') ?>
                        </option>
                    </select>
                </div>

                <div class="form-group"
                    id="ccm-autonav-page-selector"<?php if ($info['displayPages'] != 'custom') {
                    ?> style="display: none"<?php
                } ?>>
                    <?=$form_page_selector->selectPage('displayPagesCID', $info['displayPagesCID']); ?>
                </div>

                <div class="form-group">
                    <label for="displaySubPages"><?= t('Child Pages') ?></label>

                    <select class='form-select' name="displaySubPages" onchange="toggleSubPageLevels(this.value);">
                        <option value="none"<?php if ($info['displaySubPages'] == 'none') {
                            ?> selected<?php } ?>>
                            <?= t('None') ?>
                        </option>
                        <option value="relevant"<?php if ($info['displaySubPages'] == 'relevant') {
                            ?> selected<?php } ?>>
                            <?= t('Relevant sub pages.') ?>
                        </option>
                        <option
                            value="relevant_breadcrumb"<?php if ($info['displaySubPages'] == 'relevant_breadcrumb') {
                            ?> selected<?php } ?>>
                            <?= t('Display breadcrumb trail.') ?>
                        </option>
                        <option value="all"<?php if ($info['displaySubPages'] == 'all') {
                            ?> selected<?php } ?>>
                            <?= t('Display all.') ?>
                        </option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="displaySubPageLevels"><?= t('Page Levels') ?></label>

                    <select class="form-select" id="displaySubPageLevels"
                            name="displaySubPageLevels" <?php if ($info['displaySubPages'] == 'none') {
                        ?> disabled <?php } ?>
                            onchange="toggleSubPageLevelsNum(this.value);">
                        <option value="enough"<?php if ($info['displaySubPageLevels'] == 'enough') {
                            ?> selected<?php } ?>>
                            <?= t('Display sub pages to current.') ?></option>
                        <option
                            value="enough_plus1"<?php if ($info['displaySubPageLevels'] == 'enough_plus1') {
                            ?> selected<?php } ?>>
                            <?= t('Display sub pages to current +1.') ?></option>
                        <option value="all"<?php if ($info['displaySubPageLevels'] == 'all') {
                            ?> selected<?php } ?>>
                            <?= t('Display all.') ?></option>
                        <option value="custom"<?php if ($info['displaySubPageLevels'] == 'custom') {
                            ?> selected<?php } ?>>
                            <?= t('Display a custom amount.') ?></option>
                    </select>

                </div>

                <div class="form-group"
                    id="divSubPageLevelsNum"<?php if ($info['displaySubPageLevels'] != 'custom') {
                    ?> style="display: none"<?php
                } ?>>
                    <div class="input-group">
                        <input type="text" name="displaySubPageLevelsNum" value="<?= $info['displaySubPageLevelsNum'] ?>"
                            class="form-control">
                        <span class="input-group-text"><?= t('levels') ?></span>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="tab-pane tab-preview" id="autonav-preview">
        <div class="autonav-preview">
            <div class="render"></div>
            <div class="cover"></div>
        </div>
    </div>
</div>

<style type="text/css">
    div.autonav-preview {
        position: relative;
    }
    div.autonav-preview div.cover {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    div.autonav-preview div.render > ul ul {
        margin-left: 25px;
        padding-left: 0;
        list-style-type: none;
    }
    div.autonav-preview div.render li a {
        padding: 0;
        display: block;
    }
    div.autonav-preview div.render li {
        width: 100%;
        display: block;
    }
</style>

<script type="application/javascript">
    Concrete.event.publish('autonav.edit.open');
</script>
