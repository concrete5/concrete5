<?php /** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Sharing\ShareThisPage\Service;

/** @var Service[] $availableServices */
/** @var Service[] $selectedServices */
?>

<fieldset>
    <legend>
        <button class="btn btn-xs float-end btn-secondary" type="button"
                id="ccm-block-share-this-page-add-service">

            <?php echo t('Add Service') ?>
        </button>

        <?php echo t('Services') ?>
    </legend>

    <div id="ccm-block-share-this-page-service-wrapper">

    </div>
</fieldset>

<script type="text/template" class="service-template">
    <div class="form-group">
        <a href="#" data-remove="service" class="float-end">
            <i class="fa fa-minus-circle"></i>
        </a>

        <label for="serviceList" class="control-label form-label">
            <?php echo t('Choose Sharing Service'); ?>
        </label>

        <select id="serviceList" name="service[]" class="form-select">
            <% _.each(availableServices, function(currentService) { %>
                <% if (selectedService.handle == currentService.handle) { %>
                <option value="<%=currentService.handle%>" selected>
                    <%=currentService.displayName%>
                </option>
                <% } else { %>
                <option value="<%=currentService.handle%>">
                    <%=currentService.displayName%>
                </option>
                <% } %>
            <% }); %>
        </select>
    </div>
</script>

<script type="text/javascript">
    $(function () {
        let selectedServices = <?php echo json_encode($selectedServices); ?>;
        let availableServices = <?php echo json_encode($availableServices); ?>;

        let _template = _.template(
            $('script.service-template').html()
        );

        _.each(selectedServices, function (selectedService) {
            $('#ccm-block-share-this-page-service-wrapper').append(
                _template({
                    availableServices: availableServices,
                    selectedService: selectedService
                })
            );
        });

        $('#ccm-block-share-this-page-add-service').on('click', function () {
            $('#ccm-block-share-this-page-service-wrapper').append(
                _template({
                    availableServices: availableServices,
                    selectedService: false
                })
            );
        });

        $('#ccm-block-share-this-page-service-wrapper').on('click', 'a[data-remove=service]', function (e) {
            e.preventDefault();
            $(this).parent().remove();
        });
    });
</script>
