<?php 

namespace Recipely\Controllers\event;

use DateTime;

class Event {
    // LE NOM
    private String $type_event;
    private DateTime $date_event;
    private DateTime $time;
    private String $location;
    private String $id_client;

    public function __construct(String $type_event, DateTime $date_event, DateTime $time, String $location, String $id_client)
    {
        $this->type_event = $type_event;
        $this->date_event = $date_event;
        $this->time = $time;
        $this->location = $location;
        $this->id_client = $id_client;
    }

    public function toArray()
    {
        return [
            'type_event' => $this->type_event,
            'date_event' => $this->date_event->format('Y-m-d'),
            'time' => $this->time->format('H:i:s'),
            'location' => $this->location,
            'id_client' => $this->id_client
        ];
    }

}