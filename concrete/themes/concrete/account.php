<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $view->inc('elements/header.php'); ?>

<div class="container">
<div class="row">
<div class="col-sm-10 col-sm-offset-1">
<?php
View::element(
    'system_errors',
    array(
        'format' => 'block',
        'error' => isset($error) ? $error : null,
        'success' => isset($success) ? $success : null,
        'message' => isset($message) ? $message : null,
    )
);
?>
</div>
</div>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header"><?=t('My Account')?></h1>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-9">

                <?php echo $innerContent ?>

            </div>
            <div class="col-sm-3">

                <?php
                $nav->render();
                ?>

            </div>
        </div>
    </div>
</div>

<?php $view->inc('elements/footer.php'); ?>