<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$resolver = $app->make(ResolverManagerInterface::class);
$image = date('Ymd') . '.jpg';
$c = \Concrete\Core\Page\Page::getCurrentPage();
$cp = new \Concrete\Core\Permission\Checker($c);
$canEdit = $cp->canEditPageContents();
$token = $app->make('token');

if ($c->getCollectionPath() != '/dashboard/welcome') {
    $welcome = Page::getByPath('/dashboard/welcome');
} else {
    $welcome = $c;
}
$nav = $welcome->getCollectionChildren();

$controller = new \Concrete\Controller\Panel\Page\CheckIn();
$controller->setPageObject($c);
$approveAction = $controller->action('submit');

if (Config::get('concrete.white_label.background_image') !== 'none' && !Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.urls.background_feed') . '/' . $image;
} else if (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}

?>

<nav class="ccm-dashboard-desktop-navbar navbar navbar-dark navbar-expand-md">
    <span class="navbar-text"><?=$app->make('date')->formatDate('now', 'full')?></span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWelcomeBack" aria-controls="navbarWelcomeBack" aria-expanded="false" aria-label="<?=h(t('Toggle navigation'))?>">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarWelcomeBack">
        <ul class="nav navbar-nav">
            <li <?php if ($c->getCollectionPath() == '/dashboard/welcome') {?>class="active"<?php } ?>><a href="<?=URL::to('/dashboard/welcome')?>" class="nav-link"><?=t('Welcome')?></a></li>
            <?php foreach($nav as $page) { ?>
                <li <?php if ($page->getCollectionID() == $c->getCollectionID()) {?>class="active"<?php } ?>>
                    <a href="<?=$page->getCollectionLink()?>" class="nav-link"><?=t($page->getCollectionName())?></a>
                </li>
            <?php } ?>
            <li><a href="<?=URL::to('/account')?>" class="nav-link"><?=t('My Account')?></a></li>
        </ul>
    </div>
</nav>

<div class="ccm-dashboard-welcome-customize">
    <form method="post" data-form="check-in" action="<?=$approveAction?>">
        <?php if ($canEdit) { ?>
            <input type="hidden" name="comments" value="<?= h(t('Welcome page updated')) ?>" />
        <?php } ?>
            <?php if ($canEdit) { ?>
                <?php if ($c->isEditMode()) { ?>
                    <a href="#" id="ccm-dashboard-welcome-check-in" class="btn btn-secondary"><?= t('Save Changes'); ?></a>
                <?php } ?>
                <?php if (!$c->isEditMode()) { ?>
                    <a href="<?= h($resolver->resolve(["/ccm/system/page/checkout/{$c->getCollectionID()}/-/" . $token->generate()])) ?>" id="ccm-nav-check-out" class="btn btn-secondary"><?= t('Customize'); ?></a>
                <?php } ?>
            <?php } ?>
        <?php if ($canEdit) { ?>
            <input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
            <input type="hidden" name="action" value="publish">
        <?php } ?>
    </form>
</div>

<?php if (isset($imagePath)) { ?>
    <style type="text/css">
        body.ccm-dashboard-desktop div#ccm-dashboard-content {
            background-image: url(<?=$imagePath?>);
        }
        body.ccm-dashboard-desktop div#ccm-dashboard-content::before {
            display: block;
        }
    </style>
<?php } ?>

<script type="text/javascript">
    $(function() {

        <?php if ($canEdit) { ?>
            $('#ccm-dashboard-welcome-check-in').on('click', function(e) {
                e.preventDefault();
                $(this).closest('form').submit();
            });

            $('form[data-form=check-in]').concreteAjaxForm();

            ConcreteEvent.on('AddBlockListAddBlock', function(event, data) {
                var editor = Concrete.getEditMode();
                var area = editor.getNextBlockArea();
                blockType = new Concrete.BlockType(data.$launcher, editor);
                blockType.addToDragArea(_.last(area.getDragAreas()));
            });

            ConcreteEvent.on('EditModeAfterInit', function(event, data) {
                var areas = data.editMode.getAreas();
                _.each(areas, function(area) {
                    area.bindEvent("EditModeAddBlocksToArea.area",
                        function(e, myData) {
                            if (myData.area === area) {
                                var arHandle = myData.area.getHandle();
                                $.fn.dialog.open({
                                    width: 550,
                                    height: 380,
                                    title: '<?=t('Add Block')?>',
                                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/add_block_list?cID=<?=$c->getCollectionID()?>&arHandle=' + encodeURIComponent(arHandle)});
                            }
                        }
                    )
                });
            });
        <?php } ?>

    });
</script>



