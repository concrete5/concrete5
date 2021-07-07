<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Sharing\SocialNetwork\Link;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Sharing\SocialNetwork\Service;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var Link[] $links */
/** @var Link[] $selectedLinks */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
?>

<div class="form-group">
    <label class="control-label form-label">
        <?php echo t('Choose Social Links to Show'); ?>
    </label>

    <div id="ccm-block-social-links-list">
        <?php if (0 == count($links)) { ?>
            <p>
                <?php echo t('You have not added any social links.'); ?>
            </p>
        <?php } ?>

        <?php foreach ($links as $link) { ?>
            <?php
            /** @var Service $service */
            $service = $link->getServiceObject();
            ?>

            <?php if ($service) { ?>
                <div class="form-check">
                    <label for="<?php echo "slID" . $link->getID(); ?>" class="form-check-label">
                        <?php echo $form->checkbox("socialService", $link->getID(), is_array($selectedLinks) && in_array($link, $selectedLinks), ["name" => "slID[]", "id" => "slID" . $link->getID()]); ?>
                        <?php echo $service->getDisplayName(); ?>
                    </label>
                    <i class="fas fa-arrows-alt"></i>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<div class="alert alert-info">
    <?php echo t(/*i18n: the two %s will be replaced with HTML code*/'Add social links %sin the dashboard%s', '<a href="' . (string)Url::to('/dashboard/system/basics/social') . '">' ,'</a>'); ?>
</div>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    #ccm-block-social-links-list {
        -webkit-user-select: none;
        position: relative;
    }

    #ccm-block-social-links-list .form-check {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        margin-bottom: 0;
        padding: 6px;
    }

    #ccm-block-social-links-list .form-check:hover {
        background: #e7e7e7;
        border-radius: 4px;
        transition: background-color .1s linear;
    }

    #ccm-block-social-links-list .form-check.ui-sortable-helper {
        background: none;
    }

    #ccm-block-social-links-list i.fa-arrows-alt {
        display: none;
        color: #666;
        cursor: move;
        margin-left: auto;
    }

    #ccm-block-social-links-list div.form-check:hover i.fa-arrows-alt {
        display: block;
    }
</style>

<script>
    $(function () {
        $('#ccm-block-social-links-list').sortable({
            axis: 'y'
        });
    });
</script>
