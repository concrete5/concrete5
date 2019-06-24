<?php
namespace Concrete\Block\CalendarEvent;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Attribute\Key\EventKey;
use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Calendar\Event\Event;
use Concrete\Core\Calendar\Event\EventOccurrence;
use Concrete\Core\Calendar\CalendarServiceProvider;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 550;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btCalendarEvent';

    public function getBlockTypeDescription()
    {
        return t("Displays a calendar event on a page.");
    }

    public function getBlockTypeName()
    {
        return t("Calendar Event");
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->requireAsset('core/calendar/event-selector');

        $eventKeys = EventKey::getList();
        $calendars = ['' => t('** Choose a Calendar')];
        $calendarEventPageKeys = ['' => t('** Choose an Event')];

        foreach (CollectionKey::getList() as $ak) {
            if ($ak->getAttributeTypeHandle() == 'calendar_event') {
                $calendarEventPageKeys[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyDisplayName();
            }
        }
        foreach (Calendar::getList() as $calendar) {
            $calendars[$calendar->getID()] = $calendar->getName();
        }

        $displayEventAttributes = array();
        if (isset($this->displayEventAttributes)) {
            $displayEventAttributes = json_decode($this->displayEventAttributes);
        }

        $this->set('calendarEventPageKeys', $calendarEventPageKeys);
        $this->set('eventKeys', $eventKeys);
        $this->set('calendars', $calendars);
        $this->set('displayEventAttributes', $displayEventAttributes);
    }

    public function save($data)
    {
        $data['calendarID'] = isset($data['calendarID']) ? intval($data['calendarID']) : 0;
        $data['eventID'] = isset($data['eventID']) ? intval($data['eventID']) : 0;

        $data['displayEventName'] = isset($data['displayEventName']) && $data['displayEventName'] ? 1 : 0;
        $data['displayEventDate'] = isset($data['displayEventDate']) && $data['displayEventDate'] ? 1 : 0;
        $data['displayEventDescription'] = isset($data['displayEventDescription']) && $data['displayEventDescription'] ? 1 : 0;
        $data['enableLinkToPage'] = isset($data['enableLinkToPage']) && $data['enableLinkToPage'] ? 1 : 0;

        $attributes = array();
        if (isset($data['displayEventAttributes']) && is_array($data['displayEventAttributes'])) {
            $attributes = $data['displayEventAttributes'];
        }
        $data['displayEventAttributes'] = json_encode($attributes);
        parent::save($data);
    }

    protected function getEvent()
    {
        $event = null;
        if ($this->mode == 'P') {
            $page = \Page::getCurrentPage();
            $event = $page->getAttribute($this->calendarEventAttributeKeyHandle);
        } else {
            if ($this->eventID) {
                $event = Event::getByID($this->eventID);
            }
        }

        return $event;
    }

    public function getOccurrence()
    {
        $event = $this->getEvent();
        if (is_object($event)) {
            if ($this->request->query->has("occurrenceID")) {
                $occurrence = EventOccurrence::getByID($this->request->query->get('occurrenceID'));
                if ($occurrence) {
                    if ($occurrence->getEvent() && $occurrence->getEvent()->getID() == $event->getID()) {
                        if ($event->isApproved()) {
                            return $occurrence;
                        }
                    }


                    if ($occurrence->getEvent() && Event::isRelatedTo($occurrence->getEvent(), $event)) {

                        // this is a temporary hack. We need to ultimately make this make sure that
                        // the event matches –but sometimes our events don't match because we imported them as series
                        // events and not as  occurrences of one event.
                        // @TODO delete this code when we no longer need it and all related events are migrated into multidate events.
                        if ($event->isApproved()) {
                            return $occurrence;
                        }
                    }
                }
            }
            $date = $this->app->make('date')->date('Y-m-d', null, 'app');
            $time = $this->app->make('date')->toDateTime($date . ' 00:00:00', 'GMT', 'app')->getTimestamp();
            $list = $event->getOccurrenceList();
            $list->filterByStartTimeAfter($time);
            $list->setItemsPerPage(1);
            $results = $list->getResults();
            if (!$results) {
                $list = $event->getOccurrenceList();
                $list->sortBy('startTime', 'desc');
                $results = $list->getResults();
            }
            return $results[0];
        } else if ($this->request->query->has('occurrenceID') && $this->mode == 'R') {
            // request mode
            $occurrence = EventOccurrence::getByID($this->request->query->get('occurrenceID'));
            if ($occurrence) {
                $event = $occurrence->getEvent();
                if ($event && $event->isApproved()) {
                    return $occurrence;
                }
            }
        }
    }

    public function view()
    {
        $this->set('event', $this->getEvent());

        $displayEventAttributes = array();
        if (isset($this->displayEventAttributes)) {
            $displayEventAttributes = json_decode($this->displayEventAttributes);
        }

        $this->set('displayEventAttributes', $displayEventAttributes);
        $occurrence = $this->getOccurrence();
        $this->set('occurrence', $occurrence);
        $provider = $this->app->make(CalendarServiceProvider::class);
        if ($occurrence) {
            $this->set('event', $occurrence->getEvent());
            $linkFormatter = $provider->getLinkFormatter();
            $eventOccurrenceLink = $linkFormatter->getEventOccurrenceFrontendViewLink($occurrence);
            $this->set('eventOccurrenceLink', $eventOccurrenceLink);
        }
        $formatter = $provider->getDateFormatter();
        $this->set('formatter', $formatter);
    }

}
