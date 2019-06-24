<?php defined('C5_EXECUTE') or die('Access Denied.');
$parents = Loader::helper('navigation')->getTrailToCollection($c);
$pageIDs = array();
foreach ($parents as $pc) {
    $pageIDs[] = $pc->getCollectionID();
}
?>
<section>
	<div data-panel-menu="accordion" class="ccm-panel-header-accordion">
	<nav>
	<span><?php if (!in_array($tab, array('favorites'))) {
    ?><?=t('Dashboard')?><?php 
} else {
    ?><?=t('Favorites')?><?php 
} ?></span>
	<ul class="ccm-panel-header-accordion-dropdown">
		<li><a data-panel-accordion-tab="dashboard" <?php if (!in_array($tab, array('favorites'))) {
    ?>data-panel-accordion-tab-selected="true" <?php 
} ?>><?=t('Dashboard')?></a></li>
		<li><a data-panel-accordion-tab="favorites" <?php if ($tab == 'favorites') {
    ?>data-panel-accordion-tab-selected="true" <?php 
} ?>><?=t('Favorites')?></a></li>
	</ul>
	</nav>
	</div>

		<?php if ($tab == 'favorites') {
    ?>
			<ul class="nav">
			<?php
			for ($i = 0; $i < count($nav); $i++) {
				$cc = $nav[$i];
				$active = ($cc->getCollectionID() == $c->getCollectionID() || (in_array($cc->getCollectionID(), $pageIDs)));
    $cp = new Permissions($cc);
    if ($cp->canViewPage()) {
        ?>
					<li <?php if ($active) {
    ?>class="nav-selected"<?php } ?>><a href="<?=Loader::helper('navigation')->getLinkToCollection($cc)?>"><?=t($cc->getCollectionName())?></a></li>

			<?php
		$next = $nav[$i + 1];
		if ($cc->getAttribute('is_desktop') || is_object($next) && $next->getPackageID() > 0 && $cc->getPackageID() == 0) {
			echo '<li class="nav-divider"></li>';
		}

		?>
				<?php
    }
    ?>
			<?php 
}
    ?>
			</ul>
		<?php 
} else {
    ?>
			<?php
			$nav->render();
		} ?>

	
	<div class="ccm-panel-dashboard-footer">
		<p><?=t('Logged in as <a href="%s">%s</a>', URL::to('/account'), $ui->getUserDisplayName());?>. </p>
		<a href="<?=URL::to('/login', 'do_logout', Loader::helper('validation/token')->generate('do_logout'))?>"><?=t('Sign Out.')?></a>
	</div>
</section>
