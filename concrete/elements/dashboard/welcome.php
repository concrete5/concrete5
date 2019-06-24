<?php
defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
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
    $imageData = Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/tools/required/dashboard/get_image_data';
} else if (Config::get('concrete.white_label.background_url')) {
    $imagePath = Config::get('concrete.white_label.background_url');
}

?>

<div class="ccm-dashboard-welcome">
    <h1><div class="ccm-dashboard-welcome-inner"><?=t('Welcome Back')?>
            <?php if (isset($imageData)) { ?>
                <a href="#" class="launch-tooltip" title="<?=t('View Original Image')?>"><i class="fa fa-image"></i></a>
            <?php } ?>
        </div>
    </h1>
</div>

<nav class="ccm-dashboard-desktop-navbar navbar navbar-default">
    <div class="container-fluid">

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <?php if ($canEdit) { ?>
                <form method="post" data-form="check-in" action="<?=$approveAction?>">
                    <input type="hidden" name="comments" value="<?= h(t('Welcome page updated')) ?>" />
            <?php } ?>
                <ul class="nav navbar-nav">
                    <li><p class="navbar-text"><?=$app->make('date')->formatDateTime('now', true, true)?></p></li>
                    <?php if ($canEdit) { ?>
                        <li>
                            <?php if ($c->isEditMode()) { ?>
                                <a href="#" id="ccm-dashboard-welcome-check-in"><?= t('Save Changes'); ?></a>
                            <?php } ?>
                            <?php if (!$c->isEditMode()) { ?>
                                <a href="<?= DIR_REL; ?>/<?= DISPATCHER_FILENAME; ?>?cID=<?= $c->getCollectionID(); ?>&ctask=check-out&<?= $token->getParameter(); ?>" id="ccm-nav-check-out"><?= t('Customize'); ?></a>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li <?php if ($c->getCollectionPath() == '/dashboard/welcome') {?>class="active"<?php } ?>><a href="<?=URL::to('/dashboard/welcome')?>"><?=t('Welcome')?></a></li>
                    <?php foreach($nav as $page) { ?>
                        <li <?php if ($page->getCollectionID() == $c->getCollectionID()) {?>class="active"<?php } ?>>
                            <a href="<?=$page->getCollectionLink()?>"><?=t($page->getCollectionName())?></a>
                        </li>
                    <?php } ?>
                    <li><a href="<?=URL::to('/account')?>"><?=t('My Account')?></a></li>
                </ul>
            <?php if ($canEdit) { ?>
                    <input type="hidden" name="cID" value="<?=$c->getCollectionID()?>">
                    <input type="hidden" name="action" value="publish">
                </form>
            <?php } ?>
        </div>
    </div>
</nav>

<?php if (isset($imagePath)) { ?>
    <style type="text/css">
        div.ccm-dashboard-welcome {
            background-image: url(<?=$imagePath?>);
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }
    </style>
<?php } ?>

<script type="text/javascript">
    $(function() {

        <?php if (isset($imageData)) { ?>
            $.getJSON('<?=$imageData?>', { image: '<?= $image ?>' }, function (data) {
                $('.ccm-dashboard-welcome-inner a').attr('href', data.link);
            });
        <?php } ?>

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



