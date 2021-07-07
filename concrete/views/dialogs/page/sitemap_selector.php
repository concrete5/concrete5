<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var int|null $cID
 * @var string $selectMode
 * @var string $uniqid
 * @var bool $includeSystemPages
 * @var bool $askIncludeSystemPages
 */
?>
<div class="ccm-ui h-100" id="ccm-sitemap-search-selector">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-3 border-right flex-column">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a data-bs-toggle="tab" id="sitemap-sitemap-tab" href="#sitemap-sitemap" class="nav-link"><?=t('Full Sitemap')?></a>
                    </li> 
                    <li class="nav-item">
                        <a data-bs-toggle="tab" id="sitemap-explore-tab" href="#sitemap-explore" class="nav-link"><?=t('Flat Sitemap')?></a>
                    </li>
                    <li class="nav-item">
                        <a data-bs-toggle="tab" id="sitemap-search-tab" href="#sitemap-search" class="nav-link"><?=t('Search')?></a>
                    </li>
                </ul>
                <?php
                if ($askIncludeSystemPages) {
                    ?>
                    <div class="form-check position-absolute" style="bottom: 0">
                        <input type="checkbox" class="form-check-input" id="sitemap-include-system-pages"<?= $includeSystemPages ? ' checked="checked"' : ''?> />
	                   <label class="form-check-label" for="sitemap-include-system-pages"><?= t('Include System Pages in Sitemap') ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="col-9 mh-100 overflow-auto">
                <div class="tab-content mh-100">
                    <div id="sitemap-sitemap" class="tab-pane"></div>
                    <div id="sitemap-explore" class="tab-pane"></div>
                    <div id="sitemap-search" class="tab-pane">
                        <div data-concrete-page-chooser-search="<?= $uniqid ?>">
                            <concrete-page-chooser-search/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(function() {
    function loadSitemapOverlay(type, url) {
        var $el = $('#sitemap-' + type);
        if ($el.html() == '') {
            $.fn.dialog.showLoader();
            $el.load(url, function() {
                $.fn.dialog.hideLoader();
            });
        }
    }
    <?php
    if ($selectMode == 'move_copy_delete') {
        ?>
        ConcreteEvent.unsubscribe('SitemapSelectPage.search');
        var subscription = function (e, data) {
            Concrete.event.unsubscribe(e);
            url = CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/page/drag_request?dragMode=none&origCID=<?= $cID ?>&destCID=' + data.cID;
            $.fn.dialog.open({
                width: 520,
                height: 'auto',
                href: url,
                title: ccmi18n_sitemap.moveCopyPage,
                onDirectClose: function() {
                    ConcreteEvent.subscribe('SitemapSelectPage.search', subscription);
                }
            });
        };
        ConcreteEvent.subscribe('SitemapSelectPage.search', subscription);
        <?php
    }
    ?>
    var cParentID = 0,
        includeSystemPages = <?= $includeSystemPages ? 'true' : 'false' ?>,
        sst = $.cookie('ccm-sitemap-selector-tab');
    ;
    if (sst !== 'explore' && sst !== 'search') {
        sst = 'sitemap';
    }
    $("a[href='#sitemap-" + sst + "']").parent().addClass('active');
    $("a[href='#sitemap-sitemap']").click(function() {
        loadSitemapOverlay('sitemap', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay?includeSystemPages=' + (includeSystemPages ? 1 : 0));
    });
    $("a[href='#sitemap-explore']").click(function() {
        loadSitemapOverlay('explore', CCM_DISPATCHER_FILENAME + '/ccm/system/page/sitemap_overlay?&display=flat&cParentID=' + cParentID + '&includeSystemPages=' + (includeSystemPages ? 1 : 0));
    });


    const $firstTab = $('#ccm-sitemap-search-selector ul li.active a')
    $firstTab.click();
    const bsFirstTab = new bootstrap.Tab($firstTab)
    bsFirstTab.show()

    $('#ccm-tab-content-sitemap').on('click', '.ccm-sitemap-open-flat-view', function(event) {
        var node = $.ui.fancytree.getNode(event);
        if (node && node.data && node.data.cParentID) {
            cParentID = node.data.cParentID;
        }
        $('#ccm-tab-content-explore').html('');
        $('a[data-tab=explore]').trigger('click');
    });
    <?php
    if ($askIncludeSystemPages) {
        ?>
        $('#sitemap-include-system-pages').on('change', function() {
            includeSystemPages = this.checked;
            $('#sitemap-sitemap,#sitemap-explore').empty();
            $('#ccm-sitemap-search-selector ul li.nav-item a.active').click();
        });
        <?php
    }
    ?>

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-concrete-page-chooser-search="<?= $uniqid ?>"]',
            components: config.components
        });
    });
});
</script>
