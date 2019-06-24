<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">

    <div class="ccm-calendar-wrapper" data-calendar="<?= $calendar->getID() ?>">


    </div>


</div>


<script type="text/javascript">
    $(function () {
        $('div[data-calendar=<?=$calendar->getID()?>]').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            events: {
                url: '<?=$view->action('get_events')?>',
                data: {
                    'caID': '<?=$calendar->getID()?>'
                }
            },
            eventRender: function(event, element) {
                element.attr('href', '#'); // Just to make the pointer nice instead of a text handle.
            },
            eventClick: function(event, jsView, view) {
                ConcreteEvent.publish('CalendarEventSearchDialogSelectEvent', {
                    id: event.id,
                    title: event.title
                });
            }
        });
        setTimeout(function() {
            // not sure why i need this to render off the bat but I do and I don't care to find out.

            $('div[data-calendar=<?=$calendar->getID()?>]').fullCalendar('render');
        }, 50);
    });
</script>
