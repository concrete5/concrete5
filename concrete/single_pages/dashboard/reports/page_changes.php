<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var \Concrete\Core\Form\Service\Widget\DateTime $dt */
/* @var \Concrete\Core\Validation\CSRF\Token $token */
?>

<form role="form" method="post" action="<?php echo $controller->action('csv_export'); ?>">
    <?php
    $token->output('export_page_changes');
    ?>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <label for="startDate" class="control-label form-label">
                    <?php echo tc('Start date', 'From'); ?>
                </label>
                <div>
                    <?php
                    echo $dt->datetime('startDate', null, true);
                    ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <label for="endDate" class="control-label form-label">
                    <?php echo tc('End date', 'To'); ?>
                </label>
                <div>
                    <?php
                    echo $dt->datetime('endDate', null, true);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-search-fields-submit">
        <button type="submit" class="btn btn-primary pull-right">
            <?php echo t('Export to CSV'); ?>
        </button>
    </div>
</form>
