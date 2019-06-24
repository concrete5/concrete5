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

<?php echo $innerContent ?>

</div>
</div>

<?php $view->inc('elements/footer.php'); ?>