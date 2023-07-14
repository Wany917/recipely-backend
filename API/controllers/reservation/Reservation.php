<?php

namespace Recipely\Controllers\reservation;

class Reservation
{
    private Int $id;
    private String $date_reservation;
    private Int $id_event;
    private String $id_client;

    public function __construct(Int $id, string $date_reservation, int $id_event, String $id_client)
    {
        $this->id = $id;
        $this->date_reservation = $date_reservation;
        $this->id_event = $id_event;
        $this->id_client = $id_client;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date_reservation' => $this->date_reservation,
            'id_event' => $this->id_event,
            'id_client' => $this->id_client,
        ];
    }

}