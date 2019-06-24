<?php 
defined('C5_EXECUTE') or die('Access Denied.');
?> 

<fieldset id="ccm_edit_pane_nextPreviousWrap">
    <div class="form-group">
        <?php
        echo $form->label('nextLabel', t('Next Label'));
        echo $form->text('nextLabel', h($nextLabel), ['placeholder' => t('leave blank to hide')]);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('previousLabel', t('Previous Label'));
        echo $form->text('previousLabel', h($previousLabel), ['placeholder' => t('leave blank to hide')]);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('parentLabel', t('Up Label'));
        echo $form->text('parentLabel', h($parentLabel), ['placeholder' => t('leave blank to hide')]);
        ?>
    </div>

    <div class="form-group">
         <div class="checkbox">
            <label>
                <?php
                echo $form->checkbox('loopSequence', 1, intval($loopSequence));
                echo t('Loop Navigation');
                ?>
            </label>
         </div>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('orderBy', t('Order Pages'));

        $options = [
            'display_asc' => t('Sitemap'),
            'chrono_desc' => t('Chronological'),
            'display_desc' => t('Reverse Sitemap'),
            'chrono_asc' => t('Reverse Chronological'),
        ];

        echo $form->select('orderBy', $options, $orderBy);
        ?>
    </div>
</fieldset>
