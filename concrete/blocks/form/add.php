<?php 
defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$ui = UserInfo::getByID($u->uID);
?>

<script type="text/javascript">
var thisbID=parseInt(<?php echo empty($_REQUEST['bID']) ? 0 : intval($_REQUEST['bID'])?>); 
var thisbtID=parseInt(<?php echo $bt->getBlockTypeID()?>); 
</script>

<?php $this->inc('form_setup_html.php'); ?>