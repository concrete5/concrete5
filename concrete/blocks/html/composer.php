<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="form-group">
    <?php
    echo $form->label($view->field('content'), $label);

    if ($description) { ?>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo $description ?>"></i>
    <?php } ?>

    <div class="controls">
        <?php
        echo $form->textarea($view->field('content'), $content, [
            'class' => $class,
            'style' => 'width: 580px; height: 380px',
        ]);
        ?>
    </div>
</div>
