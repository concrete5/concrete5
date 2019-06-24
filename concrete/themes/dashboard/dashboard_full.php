<?php
defined('C5_EXECUTE') or die('Access Denied.');
$this->inc('elements/header.php', array('enableEditing' => true));
?>

<div class="ccm-ui">
<div id="newsflow-main">
<?php $this->inc('elements/header_newsflow.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <?php $a = new Area('Main'); $a->display($c); ?>
        </div>
    </div>
</div>
</div>

<?php $this->inc('elements/footer.php'); ?>