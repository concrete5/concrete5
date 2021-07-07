<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
?>

<div class="ccm-header-search-form ccm-ui" data-header="express">

    <form method="get" class="row row-cols-auto g-0 align-items-center" action="<?=$headerSearchAction?>">

        <div class="col-auto">
            <a href="#" data-launch-dialog="advanced-search" class="ccm-header-launch-advanced-search"
                <?php if (isset($query)) { ?>
                    data-advanced-search-query="advanced-search-query"
                <?php } ?>
               data-advanced-search-dialog-url="<?php echo URL::to('/ccm/system/dialogs/express/advanced_search') ?>?cID=<?=$c->getCollectionID()?>&amp;exEntityID=<?=$entity->getId()?>">
                <?= t('Advanced') ?>


                <?php if (isset($query)) { ?>
                    <script type="text/concrete-query" data-query="advanced-search-query">
                    <?=$query?>
                </script>
                <?php } ?>

            </a>
        </div>
        
        <div class="col-auto">
            <div class="ccm-header-search-form-input input-group">
                <?=$form->search('keywords', [
                    'placeholder' => t('Search'),
                    'class' => 'border-end-0',
                    'autocomplete' => 'off']);
                ?>
                <button type="submit" class="input-group-icon">
                    <svg width="16" height="16"><use xlink:href="#icon-search"/></svg>
                </button>
            </div>
        </div>

    </form>

</div>

<script type="text/javascript">
    $(function() {
        ConcreteEvent.subscribe('SavedSearchCreated', function(e, data) {
            window.location.reload();
        });
        ConcreteEvent.subscribe('SavedPresetSubmit', function(e, url) {
            window.location.href = url;
        });
    });


</script>
