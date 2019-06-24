<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="form-group">
	<label><?=$label?></label>
	<?php if ($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<?php
    $content = $controller->getContentEditMode();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = $view->getRequestValue();
        $content = $data['content'];
    }
    echo Core::make('editor')->outputPageComposerEditor($view->field('content'), $content);
    ?>
</div>