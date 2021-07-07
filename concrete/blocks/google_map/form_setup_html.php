<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $title
 * @var string $location
 * @var string $latitude
 * @var string $longitude
 * @var int $zoom
 * @var string $width
 * @var string $height
 * @var int $scrollwheel
 */
?>

<div class="ccm-google-map-block-container row">
    <div class="col-12">
        <div class="form-group">
            <?= $form->label('apiKey', t('API Key') . ' <i class="fa fa-question-circle launch-tooltip" title="' . t('The API Key must be enabled for Google Maps and Google Places.') . "\n" . t('API keys can be obtained in the Google Developers Console.') . '"></i>') ?>
            <div class="input-group">
                <?= $form->text('apiKey', Config::get('app.api_keys.google.maps')) ?>
                <span class="input-group-text btn btn-secondary" id="ccm-google-map-check-key">
                    <?= t('Check') ?>
                    &nbsp;
                    <i id="ccm-google-map-check-key-spinner" class="fas fa-play"></i>
                </span>
            </div>
            <div id="block_note" class="alert alert-info" role="alert"><?= t('Checking API Key...') ?></div>
        </div>

        <div class="form-group">
            <?= $form->label('title', t('Map Title (optional)')) ?>
		    <div class="input-group">
            	<?= $form->text('title', $title) ?>
				<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
			</div>
		</div>

        <div id="ccm-google-map-block-location" class="form-group">
            <?= $form->label('location', t('Location') . ' <i class="fa fa-question-circle launch-tooltip" title="' . t('Start typing a location (e.g. Apple Store or 235 W 3rd, New York) then click on the correct entry on the list.') . '"></i>') ?>
            <?= $form->text('location', $location) ?>
            <?= $form->hidden('latitude', $latitude) ?>
            <?= $form->hidden('longitude', $longitude) ?>
            <div id="map-canvas"></div>
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <?= $form->label('zoom', t('Zoom')) ?>
            <?php
                $zoomLevels = range(0, 21);
                $zoomArray = array_combine($zoomLevels, $zoomLevels);
            ?>
            <?= $form->select('zoom', $zoomArray, $zoom) ?>
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <?= $form->label('width', t('Map Width')) ?>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-arrows-alt-h"></i></span>
                <?= $form->text('width', $width) ?>
            </div>
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <?= $form->label('height', t('Map Height')) ?>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-arrows-alt-v"></i></span>
                <?= $form->text('height', $height) ?>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('scrollwheel', 1, $scrollwheel) ?>
                <?= $form->label('scrollwheel', t('Enable Scroll Wheel'), ['class' => 'form-check-label']) ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        'use strict';

        var $key = $('#apiKey'),
            $location = $('#ccm-google-map-block-location > input[id="location"]'),
            $note = $("#block_note");

        var setupApiKey = (function () {
            var $checkSpinner = $('#ccm-google-map-check-key-spinner'),
                checking = false,
                $script = null,
                originalGMAuthFailure = window.gm_authFailure,
                lastKey = null,
                lastKeyError = null,
                autocomplete = null;

            function setAutocompletion(places) {
                if (autocomplete) {
                    $location.removeAttr('placeholder autocomplete disabled style').removeClass('gm-err-autocomplete notfound');
                    google.maps.event.removeListener(autocomplete.listener);
                    google.maps.event.clearInstanceListeners(autocomplete.autocomplete);
                    clearInterval(autocomplete.pacTimer);
                    $('.pac-container').remove();
                    $location.off('change');
                    autocomplete = null;
                }
                if (!places) {
                    return;
                }
                autocomplete = {
                    autocomplete: new google.maps.places.Autocomplete($location[0])
                }
                autocomplete.listener = google.maps.event.addListener(autocomplete.autocomplete, 'place_changed', function () {
                    if (autocomplete === null) {
                        return;
                    }
                    var place = autocomplete.autocomplete.getPlace();
                    if (!place.geometry) {
                        $location.addClass('notfound');
                        $note.text(<?= json_encode(t('The place you entered could not be found.')) ?>).removeClass('alert-info alert-success').removeClass('alert-danger').css('visibility', '');
                    } else {
                        $('#ccm-google-map-block-location > input[id=latitude]').val(place.geometry.location.lat());
                        $('#ccm-google-map-block-location > input[id=longitude]').val(place.geometry.location.lng());
                        $location.removeClass('notfound');
                        $note.css('visibility', 'hidden');
                    }
                });
                $location.on('change', function () {
                    $location.addClass('notfound');
                });
                autocomplete.pacTimer = setInterval(function () {
                    $('.pac-container').css('z-index', '2000');
                    if ($('#ccm-google-map-block-location > input[id="location"]').length === 0) {
                        setAutocompletion(null);
                    }
                }, 250);
            }

            return function (onSuccess, onError, forceRecheck) {
                if (checking) {
                    onError(<?= json_encode(t('Please wait, operation in progress.')) ?>);
                    return;
                }
                if (!onSuccess) {
                    onSuccess = function () {
                    };
                }
                if (!onError) {
                    onError = function () {
                    };
                }
                var key = $.trim($key.val());
                if (key === lastKey && !forceRecheck) {
                    if (lastKeyError === null) {
                        onSuccess();
                    } else {
                        onError(lastKeyError);
                    }
                    return;
                }

                function completed(places) {
                    setAutocompletion(places);
                    if (lastKeyError === null) {
                        onSuccess();
                    } else {
                        onError(lastKeyError);
                    }
                }

                setAutocompletion();
                checking = true;
                if ($script !== null) {
                    $script.remove();
                    $script = null;
                }
                var scriptLoadedFunctionName;
                for (var i = 0; ; i++) {
                    scriptLoadedFunctionName = '_ccm_gmapblock_loaded_' + i;
                    if (typeof window[scriptLoadedFunctionName] === 'undefined') {
                        break;
                    }
                }

                function scriptLoaded(error) {
                    delete window[scriptLoadedFunctionName];

                    function placesLoaded(error, places) {
                        if (originalGMAuthFailure) {
                            window.gm_authFailure = originalGMAuthFailure;
                        } else {
                            delete window.gm_authFailure;
                        }
                        $checkSpinner.removeClass('fa-refresh fa-spin').addClass('fa-play');
                        lastKey = key;
                        lastKeyError = error;
                        setTimeout(function () {
                            checking = false;
                            completed(places)
                        }, 10);
                    }

                    if (error !== null) {
                        placesLoaded(error);
                        return;
                    }
                    var places = new google.maps.places.PlacesService(document.createElement('div'));
                    window.gm_authFailure = function () {
                        placesLoaded(<?= json_encode(t('The API Key is NOT valid.')) ?>);
                    };
                    places.getDetails(
                        {
                            placeId: 'ChIJJ3SpfQsLlVQRkYXR9ua5Nhw'
                        },
                        function (place, status) {
                            if (status === 'REQUEST_DENIED') {
                                placesLoaded(<?= json_encode(t('The API Key is NOT valid for Google Places or not linked to billing account.')) ?>);
                            } else {
                                placesLoaded(null, places);
                            }
                        }
                    );
                }

                window[scriptLoadedFunctionName] = function () {
                    scriptLoaded(null);
                };
                window.gm_authFailure = function () {
                    scriptLoaded(<?= json_encode(t('The API Key is NOT valid.')) ?>);
                };
                $checkSpinner.removeClass('fa-play').addClass('fa-refresh fa-spin');
                $(document.body).append($script = $('<' + 'script src="https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&libraries=places&callback=' + encodeURIComponent(scriptLoadedFunctionName) + '"></' + 'script>'));
            };
        })();

        $key.on('change keydown keypress', function () {
            $note.html('&nbsp;').css('visibility', 'hidden');
        });
        $('#ccm-google-map-check-key')
            .on('click', function (e) {
                e.preventDefault();
                $note.text(<?= json_encode(t('Checking API Key...')) ?>).removeClass('alert-success alert-danger').addClass('alert-info').css('visibility', '');
                setupApiKey(
                    function () {
                        $note.text(<?= json_encode(t('The API Key is valid.')) ?>).removeClass('alert-info alert-danger').addClass('alert-success');
                    },
                    function (err) {
                        $note.text(err).removeClass('alert-success alert-info').addClass('alert-danger');
                    },
                    true
                );
            })
            .trigger('click')
        ;

        $location.on('keydown', function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
            }
        });

    }());
</script>
