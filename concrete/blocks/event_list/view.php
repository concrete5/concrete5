<?php

defined('C5_EXECUTE') or die('Access Denied.');

if ($calendar) {
    $pagination = $list->getPagination();
    $pagination->setMaxPerPage($totalToRetrieve);
    $events = $pagination->getCurrentPageResults();

    if ($canViewCalendar) {
        $service = Core::make('helper/date');
        $c = Page::getCurrentPage();
        $cID = $c->getCollectionID();
        $numEvents = count($events);
        ?>
    <div class="ccm-block-calendar-event-list-wrapper widget-featured-events unbound" data-page="<?= $totalPerPage ?: 3 ?>">
    <?php if ($eventListTitle) {
    ?>
        <h2><?=$eventListTitle?></h2>
    <?php

}
        ?>
        <div class="ccm-block-calendar-event-list" <?php if ($numEvents > $totalPerPage) {
    echo 'style="display:none"';
}
        ?>>

    <?php
    if ($total = count($events)) {
        foreach ($events as $occurrence) {
            $event = $occurrence->getEvent();
            $description = $event->getDescription();
            $safe_name = strtolower($event->getName());
            $safe_name = preg_replace('/[^a-z ]/i', '', $safe_name);
            $safe_name = str_replace(' ', '+', $safe_name);
            ?>
            <div class="ccm-block-calendar-event-list-event">
                <div class="ccm-block-calendar-event-list-event-date">
                    <span><?=$service->formatCustom('M', $occurrence->getStart())?></span>
                    <span><?=$service->formatCustom('d', $occurrence->getStart())?></span>
                </div>
                <div class="ccm-block-calendar-event-list-event-title">
                    <?php
                    $url = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
                    if ($url) {
    ?>
                        <a href="<?= $url ?>"><?= h($event->getName()) ?></a>
                    <?php

} else {
    ?>
                        <?= h($event->getName()) ?>
                    <?php

}
            ?>
                </div>
                <div class="ccm-block-calendar-event-list-event-date-full">
                    <?php
                        echo $formatter->getOccurrenceDateString($occurrence);
                    ?>
                </div>
                <?php if ($description) {
    ?>
                    <div class="ccm-block-calendar-event-list-event-description">
                        <?=$description?>
                    </div>
                <?php

}
            ?>
            </div>

        <?php

        }
        ?>

    <?php

    } else {
        ?>
        <p class="ccm-block-calendar-event-list-no-events"><?=t('There are no upcoming events.')?></p>
    <?php

    }
        ?>

        </div>

        <div class="btn-group ccm-block-calendar-event-list-controls">
            <?php if ($numEvents > $totalPerPage) {
    ?>
            <button type="button" class="btn btn-default" data-cycle="previous"><i class="fa fa-angle-left"></i></button>
                <button type="button" class="btn btn-default" data-cycle="next"><i class="fa fa-angle-right"></i></button>
            <?php

}
        ?>
        </div>

        <?php if ($linkToPage) { ?>
            <div class="ccm-block-calendar-event-list-link">
                <a href="<?=$linkToPage->getCollectionLink()?>"><?=t('View Calendar')?></a>
            </div>
        <?php } ?>

    </div>
    <?php if ($numEvents > $totalPerPage) {
    ?>
    <script>
        (function() {
            function Button(element) {
                this.element = element;
            }

            Button.prototype.disable = function() {
                this.element.prop('disabled', true).addClass('disabled');
                return this;
            };

            Button.prototype.enable = function() {
                this.element.prop('disabled', false).removeClass('disabled');
                return this;
            };

            var routine = function() {
                $('.ccm-block-calendar-event-list-wrapper.unbound').removeClass('unbound').each(function(){
                    var my = $(this),
                        previous  = new Button($('button[data-cycle=previous]', my)),
                        next      = new Button($('button[data-cycle=next]', my)),
                        page      = my.data('page'),
                        list      = my.children('.ccm-block-calendar-event-list'),
                        events    = list.children(),
                        start     = 0,
                        container = $('<div />').css({
                            position: 'relative',
                            overflow: 'hidden'
                        }),
                        set_container = $('<div />'),
                        slider    = $('<div />').css({
                            position: 'absolute',
                            top: 0,
                            left: 0
                        }),
                        sliding = false;

                    list.replaceWith(container);

                    events.slice(start, page).appendTo(set_container.appendTo(container));
                    container.height(container.height());

                    previous.element.click(function(){

                        if (!sliding && start >= page) {
                            sliding = true;
                            start -= page;

                            var subset = events.slice(start, start + page);

                            slide(-1, subset, function() {
                                sliding = false;
                            });

                            if (!start) {
                                previous.disable();
                            }
                            next.enable();
                        }

                        return false;
                    });

                    next.element.click(function(){
                        if (!sliding || start + 1 >= events.length) {
                            sliding = true;
                            start += page;

                            var subset = events.slice(start, start + page);

                            slide(1, subset, function() {
                                sliding = false;
                            });


                            if (start + page >= events.length) {
                                next.disable();
                            }

                            previous.enable();
                        }

                        return false;
                    });

                    if (!start) {
                        previous.disable();
                    }

                    if (start + page > events.length) {
                        next.disable();
                    }


                    function slide(direction, subset, callback, length) {
                        length = length || 750;
                        slider.empty().append(subset).height(container.height()).width(container.width()).appendTo(container);
                        if (direction > 0) {
                            set_container.css({
                                position: 'absolute',
                                top: 0,
                                left: 0,
                                width: container.width()
                            }).animate({
                                left: -container.width()
                            }, length);
                            slider.css('left', container.width()).animate({left: 0}, length, function() {
                                set_container.empty().css({
                                    position: 'static',
                                    left: 0
                                }).append(subset);
                                slider.remove();
                                callback.apply(this, Array.prototype.slice.call(arguments));
                            });
                        } else {
                            set_container.css({
                                position: 'absolute',
                                top: 0,
                                left: 0,
                                width: container.width()
                            }).animate({
                                left: container.width()
                            }, length);
                            slider.css('left', -container.width()).animate({left: 0}, length, function() {
                                set_container.empty().css({
                                    position: 'static',
                                    left: 0
                                }).append(subset);
                                slider.remove();
                                callback.apply(this, Array.prototype.slice.call(arguments));
                            });
                        }
                    }

                });
            };

            if (typeof jQuery != 'undefined') {
                routine();
            } else {
                window.addEventListener('load', routine);
            }

        }());
    </script>
    <?php

}
    }
    ?>

<?php
} ?>
