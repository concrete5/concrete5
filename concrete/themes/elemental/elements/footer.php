<?php defined('C5_EXECUTE') or die('Access Denied.');

$footerSiteTitle = new GlobalArea('Footer Site Title');
$footerSiteTitleBlocks = $footerSiteTitle->getTotalBlocksInArea();

$footerSocial = new GlobalArea('Footer Social');
$footerSocialBlocks = $footerSocial->getTotalBlocksInArea();

$displayFirstSection = $footerSiteTitleBlocks > 0 || $footerSocialBlocks > 0 || $c->isEditMode();
?>

<footer id="footer-theme">
    <?php
    if ($displayFirstSection) {
        ?>
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-9">
                        <?php
                        $a = new GlobalArea('Footer Site Title');
                        $a->display();
                        ?>
                    </div>
                    <div class="col-sm-3">
                        <?php
                        $a = new GlobalArea('Footer Social');
                        $a->display();
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
    ?>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <?php
                    $a = new GlobalArea('Footer Legal');
                    $a->display();
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php
                    $a = new GlobalArea('Footer Navigation');
                    $a->display();
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php
                    $a = new GlobalArea('Footer Contact');
                    $a->display();
                    ?>
                </div>
            </div>
        </div>
    </section>
</footer>

<footer id="concrete5-brand">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <span><?php echo t('Built with <a href="http://www.concrete5.org" class="concrete5" rel="nofollow">concrete5</a> CMS.') ?></span>
                <span class="pull-right">
                    <?php echo Core::make('helper/navigation')->getLogInOutLink() ?>
                </span>
                <span id="ccm-account-menu-container"></span>
            </div>
        </div>
    </div>
</footer>

<?php $this->inc('elements/footer_bottom.php');?>
