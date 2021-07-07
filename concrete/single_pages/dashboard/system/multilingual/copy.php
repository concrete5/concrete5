<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;

?>

<?php if (count($locales) > 1) {
    ?>

    <fieldset>
        <legend><?php echo t('Copy Locale Tree') ?></legend>
        <?php
        $u = Core::make(Concrete\Core\User\User::class);
        $copyLocales = array();
        foreach ($locales as $pc) {
            $copyLocales[$pc->getSiteTree()->getSiteHomePageID()] = tc(/*i18n: %1$s is a page name, %2$s is a language name, %3$s is a locale identifier (eg en_US)*/
                'PageWithLocale', '%1$s (%2$s)', $pc->getLanguageText(), $pc->getLocale());
        }

        if ($u->isSuperUser()) { ?>
            <form method="post" id="ccm-internationalization-copy-tree" action="#">
                <?php if (count($locales) > 1) {
                    $copyLocaleSelect1 = $form->select('copyTreeFrom', $copyLocales);
                    $copyLocaleSelect2 = $form->select('copyTreeTo', $copyLocales);
                    ?>
                    <p><?php echo t('Copy all pages from a locale to another section. This will only copy pages that have not been associated. It will not replace or remove any pages from the destination section.') ?></p>
                    <div class="form-group">
                        <label class="control-label form-label"><?php echo t('Copy From') ?></label>
                        <?php echo $copyLocaleSelect1 ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label form-label"><?php echo tc('Destination', 'To') ?></label>
                        <?php echo $copyLocaleSelect2 ?>
                    </div>

                    <?php echo Loader::helper('validation/token')->output('copy_tree') ?>
                    <div class="form-group">
                        <button class="btn btn-secondary float-start" type="submit" name="copy"><?= t('Copy Tree') ?></button>
                    </div>

                    <?php
                } elseif (count($locales) == 1) {
                    ?>
                    <p><?php echo t("You must have more than one locale to use this tool.") ?></p>
                    <?php
                } else {
                    ?>
                    <p><?php echo t('You have not created any multilingual content sections yet.') ?></p>
                    <?php
                }
                ?>

                <script type="text/javascript">
                    $(function () {
                        $("#ccm-internationalization-copy-tree").on('submit', function () {
                            var ctf = $('select[name=copyTreeFrom]').val();
                            var ctt = $('select[name=copyTreeTo]').val();
                            if (ctt > 0 && ctf > 0 && ctt != ctf) {
                                new ConcreteProgressiveOperation({
                                    url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request/copy_all',
                                    data: [
                                        {'name': 'origCID', 'value': ctf},
                                        {'name': 'destCID', 'value': ctt},
                                        {'name': 'copyChildrenOnly', 'value': true},
                                        {name: <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>, value: <?= json_encode($token->generate('/dialogs/page/drag_request'))?>}
                                    ],
                                    title: "<?=t('Copy Locale Tree')?>",
                                    onComplete: function() {
                                        window.location.href = <?=json_encode((string) $this->action('tree_copied')) ?>;
                                    }
                                });
                            } else {
                                alert(<?= json_encode(t('You must choose two separate multilingual sections to copy from/to')) ?>);
                            }
                            return false;
                        });
                    });
                </script>

            </form>
            <?php
        } elseif (!$u->isSuperUser()) {
            ?>
            <p><?= t('Only the super user may copy locale trees.') ?></p>
        <?php } ?>
    </fieldset>

    <hr/>

    <fieldset>
        <legend><?php echo t('Rescan Multilingual Tree') ?></legend>
        <?php
        if ($u->isSuperUser()) {
            ?>
            <form method="post" id="ccm-internationalization-rescan-tree" action="#">
                <?php if (count($locales) > 1) {
                    ?>
                    <p><?php echo t('Scans all blocks within the selected section. Any links to pages within another multilingual section will be updated to link to the pages within the selected tree. Any blocks within the scanned section that reference pages in another multilingual section will be updated to point to the page within the selected tree.') ?></p>
                    <div class="form-group">
                        <label class="control-label form-label"><?php echo t('Rescan Locale') ?></label>
                        <?php echo $form->select('rescanLocale', $copyLocales);
                        ?>
                    </div>

                    <?php echo Loader::helper('validation/token')->output('rescan_locale') ?>
                    <div class="form-group">
                        <button class="btn btn-secondary float-start" type="submit"
                                name="rescan_locale"><?= t('Rescan Locale') ?></button>
                    </div>

                    <?php
                } elseif (count($pages) == 1) {
                    ?>
                    <p><?php echo t("You must have more than one multilingual section to use this tool.") ?></p>
                    <?php
                } else {
                    ?>
                    <p><?php echo t('You have not created any multilingual content sections yet.') ?></p>
                    <?php
                }
                ?>

                <script type="text/javascript">
                    $(function () {
                        $("#ccm-internationalization-rescan-tree").on('submit', function () {
                            var ctf = $('select[name=rescanLocale]').val();
                            if (ctf > 0) {
                                new ConcreteProgressiveOperation({
                                    url: '<?=$view->action('rescan_locale')?>',
                                    data: [
                                        {'name': 'locale', 'value': ctf},
                                        {
                                            'name': 'ccm_token',
                                            'value': '<?=Core::make('token')->generate('rescan_locale')?>'
                                        }
                                    ],
                                    title: "<?=t('Rescan Links')?>",
                                    onComplete: function() {
                                        window.location.href = "<?=$this->action('links_rescanned')?>";
                                    }
                                });
                            }
                            return false;
                        });
                    });
                </script>

            </form>
            <?php
        } elseif (!$u->isSuperUser()) {
            ?>
            <p><?= t('Only the super user may rescan the links inside a multilingual tree.') ?></p>
            <?php
        }
        ?>
    </fieldset>

    <?php
} else {
    ?>
    <p><?php echo t('You have not created any multilingual content sections yet.');
        ?></p>
    <?php
} ?>
