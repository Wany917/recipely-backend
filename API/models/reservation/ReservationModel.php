<?php
namespace Recipely\Models\reservation;

use PDO,PDOException;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\reservation\Reservation;
use Recipely\Utils\ReservationExceptions;

class ReservationModel
{
    public function insertReservation(Reservation $reservation)
    {
        try {

            $pdo = ConnectDb::getInstance();
            $sql_insert = "INSERT INTO reservations(id,date_reservation,id_event,id_client) 
            VALUES(:id,:date_reservation,:id_event,:id_client)";

            $stmt = $pdo->prepare($sql_insert);

            $params = [
                ':id' => $reservation->toArray()['id'],
                ':date_reservation' => $reservation->toArray()['date_reservation'],
                ':id_event' => $reservation->toArray()['id_event'],
                ':id_client' => $reservation->toArray()['id_client']
            ];
            $stmt->execute($params);
            ConnectDb::closeConnexion();
            return $reservation->toArray();
        } catch (ReservationExceptions $e) {
            ConnectDb::closeConnexion();
            return $e->notCreated();
        }
    }

    public function getAllReservations(): array
    {
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id,date_reservation,id_event,id_client FROM reservations");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(empty($result)){
                return [];
            }
            ConnectDb::closeConnexion();
            return $result;
        } catch(ReservationExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }


    public function getReservation($id)
    {
        try {
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id,date_reservation,id_event,id_client FROM reservations WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            ConnectDb::closeConnexion();
            return $result;
        } catch (ReservationExceptions $e) {
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllReservationsByUser($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare("SELECT id,date_reservation,id_event,id_client FROM reservations WHERE id_client = :id");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            ConnectDb::closeConnexion();
            return $result;
        } catch(ReservationExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function updateReservation($reservation_id, $reservation_data)
    {
        $reservation_data = json_decode($reservation_data, true);
        try {
            $pdo = ConnectDb::getInstance();
            $checkId = $this->checkId($reservation_id, 'id', 'reservations');

            if ($checkId) {
                $pdo->beginTransaction();
                $paramsReservation = [':id' => $reservation_id,];

                $updateReservation = [];
                foreach ($reservation_data as $key => $value) {
                    if ($key != 'id') {
                        $updateReservation[] = $key . ' = :' . $key;
                        $paramsReservation[':' . $key] = $value;
                    }
                }

                // Mettre à jour les informations de la reservation générale
                $updateReservationQuery = "UPDATE reservations SET " . implode(', ', $updateReservation) . " WHERE id = :id";
                $stmt = $pdo->prepare($updateReservationQuery);
                $stmt->execute($paramsReservation);

                $pdo->commit();
                ConnectDb::closeConnexion();
                return $reservation_data;

            } else {
                ConnectDb::closeConnexion();
                return throw new ReservationExceptions('Reservation not found');
            }
        } catch (ReservationExceptions $e) {
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }

    public function deleteReservation($reservation_id)
    {
        try {
            $pdo = ConnectDb::getInstance();
            $checkId = $this->checkId($reservation_id, 'id', 'reservations');
            if ($checkId) {
                // suppression de la reservation
                $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");
                $stmt->execute([':id' => $reservation_id]);
                ConnectDb::closeConnexion();
                return true;
            } else {
                ConnectDb::closeConnexion();
                return throw new ReservationExceptions('Reservation not found');
            }
        } catch (ReservationExceptions $e) {
            ConnectDb::closeConnexion();
            return $e->notDeleted();
            
        }
    }

    public function checkId($value, $column, $table)
    {
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if ($fetch) {
            return true;
        } else {
            return false;
        }
    }

}