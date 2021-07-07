<?php
use Concrete\Core\Device\Device;

?>

<section id="ccm-panel-page-devices" class="ccm-ui">

    <header>
        <a href="" data-panel-navigation="back" class="ccm-panel-back">
            <svg><use xlink:href="#icon-arrow-left" /></svg>
            <?= t('Page Settings') ?>
        </a>
        <h5><?= t('Devices') ?></h5>
    </header>

    <div class="ccm-panel-content-inner">

        <?php
        $preview_url = \URL::to('/ccm/system/panels/page/devices/preview');
        $preview_url = $preview_url->setQuery(array('cID' => \Page::getCurrentPage()->getCollectionID()));

        $manager = \Core::make('device/manager');

        $organized = array();

        $devices = $manager->getList();

        foreach ($devices as $device) {
            $type = $device->getType();

            if ($type == Device::UNKNOWN) {
                $organized[Device::UNKNOWN][] = $device;
            } elseif ($type & Device::MOBILE) {
                $organized[Device::MOBILE][] = $device;
            } elseif ($type & Device::TABLET) {
                $organized[Device::TABLET][] = $device;
            } elseif ($type & Device::DESKTOP) {
                $organized[Device::DESKTOP][] = $device;
            }
        }

        $categories = array(
            Device::UNKNOWN => t('General'),
            Device::MOBILE => t('Phone'),
            Device::TABLET => t('Tablet'),
            Device::DESKTOP => t('Desktop'), );

        /**
         * @var int
         * @var Device[] $device_list
         */
        foreach ($organized as $type => $device_list) {
            ?>

            <div class="panel-page-devices-title"><?= $categories[$type] ?></div>
            <div class="ccm-menu-device-set">
                <ul>
                    <?php
                    $page = \Page::getCurrentPage();
            foreach ($device_list as $device) {
                $device_preview_url = $preview_url->setQuery(array(
                            'cID' => $page->getCollectionID(),
                            'cvID' => $page->getVersionID(),
                            'device' => $device->getHandle(), ));
                ?>
                        <li class="ccm-panel-devicelist-device"
                            data-device-brand="<?= h($device->getBrand()) ?>"
                            data-device-name="<?= h($device->getName()) ?>"
                            data-device-handle="<?= h($device->getHandle()) ?>"
                            data-device-width="<?= h($device->getWidth()) ?>"
                            data-device-height="<?= h($device->getHeight()) ?>"
                            data-device-agent="<?= h($device->getUserAgent()) ?>"
                            data-device-ratio="<?= h($device->getPixelRatio()) ?>"
                            data-device-type="<?= $type ?>"
                            data-device-preview-url="<?= $device_preview_url ?>"
                            data-device-orientation="<?= h($device->getDefaultOrientation()) ?>">
                            <script type="text/html" class="viewport"><?= $device->getViewportHTML() ?></script>
                            <div class="d-grid">
                                <a href="#" class="ccm-device-select btn btn-secondary btn-sm btn-block">
                                    <div class="ccm-panel-device-name">
                                        <div class="ccm-panel-device-name-icon">
                                            <i class="<?= $device->getIconClass() ?>"></i>
                                        </div>
                                        <div class="ccm-panel-device-name-label">
                                            <span><?= h($device->getName()) ?></span>
                                        </div>
                                    </div>
                                    <div class="ccm-panel-device-resolution">
                                        <?php
                                        $deviceWidth = h($device->getWidth());
                                        $deviceHeight = h($device->getHeight());
                                        $pixelRatio = h($device->getPixelRatio());
                                        $displayedWidth = floor($device->getWidth() / $device->getPixelRatio());
                                        $displayedHeight = floor($device->getHeight() / $device->getPixelRatio());

                                        echo "$deviceWidth x  $deviceHeight @{$pixelRatio}x ($displayedWidth x $displayedHeight)";
                                        ?>
                                    </div>
                                </a>
                            </div>
                        </li>
                    <?php

            }
            ?>
                </ul>
            </div>
        <?php

        }
        ?>
    </div>
</section>


<script>
    $(function () {

        var actions, container, orientation_buttons;

        Concrete.event.unbind('PanelLoad.mobilepreview');
        Concrete.event.bind('PanelLoad.mobilepreview', function (e, data) {
            if (data.panel) {
                panel = data.panel;
            }
            _.defer(function () {

                var setup = function() {
                    orientation_buttons = actions.children('.ccm-device-orientation').children().click(function () {
                        var me = $(this);
                        if (me.hasClass('active')) return false;

                        var orientation = 'landscape';
                        if (me.hasClass('ccm-device-portrait')) {
                            orientation = 'portrait';
                        }

                        Concrete.event.fire('DeviceOrientationChange', {orientation: orientation});
                    });

                    $('.ccm-panel-detail-device-exit').click(function() {
                        data.panel.goBack();
                    });
                };


                actions = $('.ccm-panel-detail-devices-actions');
                container = $('.ccm-panel-detail-devices-container');
                if (!actions.length || !container.length) {
                    Concrete.event.bind('PanelOpenDetail.mobilepreview', function(e) {
                        actions = $('.ccm-panel-detail-devices-actions');
                        container = $('.ccm-panel-detail-devices-container');
                        setup();
                        Concrete.event.unbind(e);
                    });
                } else {
                    setup();
                }
            });
        });

        $('.ccm-device-select').click(function () {
            var device = $(this).closest('.ccm-panel-devicelist-device'),
                device_template = device.children('.viewport').html(),
                url = device.data('device-preview-url'),
                handle = device.data('device-handle'),
                width = device.data('device-width'),
                height = device.data('device-height'),
                ratio = device.data('device-ratio'),
                orientation = device.data('device-orientation'),
                template = _.template(device_template),
                viewport = $(template({device: device}));

            $('.ccm-device-select.active').removeClass('active');
            $(this).addClass('active');

            container.empty()
                .addClass('ccm-device-preview')
                .append(viewport);


            _.defer(function () {
                viewport.find('.ccm-display-frame').attr('src', url);
            });

            var setOrientation = function (orientation) {
                container.removeClass('ccm-device-orientation-portrait ccm-device-orientation-landscape')
                    .addClass('ccm-device-orientation-' + orientation);

                viewport.find('.ccm-display-frame').css({
                    width: (orientation == 'landscape' ? width : height) / ratio,
                    height: (orientation == 'landscape' ? height : width) / ratio
                });

                orientation_buttons.removeClass('active').filter('.ccm-device-' + orientation).addClass('active');
            };

            setOrientation(orientation);

            Concrete.event.unbind('DeviceOrientationChange.mobilepreview');
            Concrete.event.bind('DeviceOrientationChange.mobilepreview', function (e, data) {
                setOrientation(data.orientation);
            });

            return false;
        });

    });
</script>
