<?php defined('C5_EXECUTE') or die('Access Denied.');  ?>

<fieldset>
    <legend>
        <button class="btn btn-xs pull-right btn-default" type="button" id="ccm-block-share-this-page-add-service"><?=t('Add Service')?></button>
        <?=t('Services')?>
    </legend>
    <div id="ccm-block-share-this-page-service-wrapper">

    </div>
</fieldset>


<script type="text/template" class="service-template">
<div class="form-group">
    <a href="#" data-remove="service" class="pull-right"><i class="fa fa-minus-circle"></i></a>
    <label class="control-label">
    <?=t('Choose Sharing Service')?></label>
    <select name="service[]" class="form-control">
        <?php foreach ($services as $service) {
    ?>
            <option value="<?=$service->getHandle()?>" <% if (service == '<?=$service->getHandle()?>') { %>selected<% } %>><?=$service->getDisplayName()?></option>
        <?php 
} ?>
    </select>
</div>
</script>

<script type="text/javascript">
$(function() {
    var selectedServices = <?=$selected?>;
    var _template = _.template(
        $('script.service-template').html()
    );
    $('#ccm-block-share-this-page-add-service').on('click', function() {
        $('#ccm-block-share-this-page-service-wrapper').append(
            _template({service: false})
        );
    });

    $('#ccm-block-share-this-page-service-wrapper').on('click', 'a[data-remove=service]', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    _.each(selectedServices, function(service) {
        $('#ccm-block-share-this-page-service-wrapper').append(
            _template({service: service})
        );
    });
});
</script>
