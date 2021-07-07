<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Package as PackageFacade;

/**
 * @var Concrete\Core\Page\Page[] $generated
 */

?>
<div class="clearfix">
    <h4><?= t('Add Single Page'); ?></h4>
    <?php
        if (Config::get('concrete.seo.url_rewriting')) {
            $base = Application::getApplicationURL();
        } else {
            $base = Application::getApplicationURL() . '/' . DISPATCHER_FILENAME;
        }
    ?>
    <form class="row row-cols-auto g-0 align-items-center" method="post" id="add_static_page_form" action="<?= $view->action('') ?>">
        <div class="col-auto">
            <?php $token->output('add_single_page'); ?>
        </div>
        <div class="col-auto">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-text"  ><?= $base; ?>/</span>
                    <input type="text" style="width: 200px" class="form-control" name="pageURL" value="<?= h($this->post('pageURL')); ?>" />
                    <button class="btn btn-secondary" type="submit"><?=t('Add'); ?></button>
                </div>
            </div>
        </div>
    </form>
    <hr>
    <h4><?= t('Already Installed'); ?></h4>
    <table border="0" cellspacing="1" cellpadding="0" class="table table-striped">
        <thead>
            <tr>
                <th class="subheader"><?= t('Name'); ?></th>
                <th class="subheader"><?= t('Path'); ?></th>
                <th class="subheader"><?= t('Package'); ?></th>
                <th class="subheader"></th>
            </tr>
        </thead>
        <?php
        if (count($generated) == 0) {
        ?>
            <tr>
                <td colspan="4">
                    <?= t('No pages found.'); ?>
                </td>
            </tr>
			<?php
        } else {
            foreach ($generated as $p) {
                $cp = new Permissions($p);
                if ($p->getPackageID() > 0) {
                    $package = PackageFacade::getByID($p->getPackageID());
                    if (is_object($package)) {
                        $packageHandle = $package->getPackageHandle();
                        $packageName = $package->getPackageName();
                    }
                } else {
                    $packageName = t('None');
                }
            ?>
                <tr>
                    <td style="width: 30%"><a href="<?=URL::to($p); ?>"><?= $p->getCollectionName(); ?></a></td>
                    <td style="width: 40%"><?= $p->getCollectionPath(); ?></td>
                    <td style="width: 30%"><?= $packageName; ?></td>
                    <td>
                        <?php if ($cp->canAdmin()) { ?>
                            <a href="<?=$view->action('refresh', $p->getCollectionID(), $token->generate('refresh')); ?>" title="<?=t('Refreshes the page, rebuilding its permissions and its name.'); ?>" class="icon-link launch-tooltip"><i class="fas fa-sync"></i></a>
                        <?php } ?>
                    </td>
                </tr>
				<?php
            } // End foreach
        } // End if
    ?>
    </table>
</div>
