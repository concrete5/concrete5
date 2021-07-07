<?php
$_error = [];
if (isset($error)) {
    if ($error instanceof Exception) {
        $_error[] = $error->getMessage();
    } elseif ($error instanceof \Concrete\Core\Error\ErrorList\ErrorList) {
        if ($error->has()) {
            $_error = $error->getList();
        }
    } else {
        $_error = $error;
    }
}
if (!empty($_error)) {
    ?>
    <div class="ccm-ui" id="ccm-dashboard-result-message">
        <?php View::element('system_errors', ['format' => 'block', 'error' => $_error]); ?>
    </div>
    <?php
}

if (isset($message)) {
    ?>
    <div class="ccm-ui" id="ccm-dashboard-result-message">
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button><?= (isset($messageIsHTML) && $messageIsHTML) ? $message : nl2br(h($message)); ?></div>
    </div>
    <?php
} elseif (isset($success)) {
    ?>
    <div class="ccm-ui" id="ccm-dashboard-result-message">
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button><?= (isset($successIsHTML) && $successIsHTML) ? $success : nl2br(h($success)); ?></div>
    </div>
    <?php
}

?>
