<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'data_sources']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <?php if (count($configuredSources)) {
            ?>

            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach ($configuredSources as $configuredSource) {
                    $source = $configuredSource->getDataSource();
                    $driver = $source->getDriver();
                    $formatter = $driver->getIconFormatter();
                    ?>

                    <li>
                        <a href="<?=$view->action('update', $configuredSource->getConfiguredDataSourceID())?>">
                            <?=$formatter->getListIconElement()?> <?=$configuredSource->getName()?>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            
            <hr/>

            <?php

        } else {
            ?>
            <p><?=t('You have not added any data sources to this board.')?></p>
            <?php

        } ?>

        <h3 class="fw-light"><?=t('Add Data Source')?></h3>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-button="attribute-type" data-bs-toggle="dropdown">
                <?=t('Choose Type')?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
                foreach ($sources as $source) {
                    /**
                     * @var \Concrete\Core\Entity\Board\DataSource $source
                     */
                    $driver = $source->getDriver();
                    $formatter = $driver->getIconFormatter();
                    ?>
                    <li><a class="dropdown-item" href="<?=$view->action('add', $board->getBoardID(), $source->getId())?>"><?=$formatter->getListIconElement()?> <?=$source->getName()?></a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
        
        
        <div class="mt-3 help-block">
            <small class="text-muted"><?=t('Note: Adding or removing a data source will reset any custom weighting rules you have applied.')?></small>
        </div>

        
    </div>
</div>
