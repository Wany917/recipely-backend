<?php

namespace Recipely\Controllers\reservation;

use PDO, Exception;
use Recipely\Lib\Response;
use Recipely\Lib\Header;
use Recipely\Lib\JsonRes;
use Recipely\Auth\Authenticator;
use Recipely\Config\ConnectDb;
use Recipely\Models\reservation\ReservationModel;
use Recipely\Controllers\reservation\Reservation;
use Recipely\Utils\ReservationExceptions;

class ReservationControllers
{

    private Response $response;
    private Header $headers;
    private ReservationExceptions $exception;
    private Authenticator $authenticator;
    
    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new ReservationExceptions;    
        $this->authenticator = new Authenticator();
    }

    private Int $id;
    private String $date_reservation;
    private Int $id_event;
    private String $id_client;

    private $errors = [];

    public function createReservation($id_event,$id_client,$reservation_data)
    {
        $this->checkData($reservation_data);
        
        $this->id_event = $id_event;
        $this->id_client = $id_client;
        $this->id = $this->generateId();

        if (empty($this->errors)) {
            $reservation = new Reservation(
                $this->id,
                $this->date_reservation,
                $this->id_event,
                $this->id_client
            );

            $reservationModel = new ReservationModel();
            $reservationResult = $reservation = $reservationModel->insertReservation($reservation);

            echo $this->response->sendResponse(200, true, 'Reservation created successfully',$reservationResult); 
            return true;
        } else {
            echo $this->response->sendResponse(400, false, 'Error while creating reservation', $this->errors);
            return false;
        }

    }

    // generate renvoie un id de type Int
    private function generateId(){
        $id = substr(uniqid(), -5);
        return intval($id);
    }

    public function checkValue($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if(empty($fetch)){
            return true;
        } else{
            return false;
        }
    }

    public function checkData($data){
        foreach($data as $key => $value){
           if(empty($value)){
                if($key == 'date_reservation'){
                    $value = date('Y-m-d H:i:s');
                }else{
                    $this->errors[] = 'Le champs ' . $key . ' est vide';
                }
           }

           $value = trim($value);
           switch($key){
                case 'date_reservation':
                    if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value)){
                        $this->errors[] = 'La date de réservation n\'est pas valide';
                    }
                    $this->date_reservation = $value;
                break;
           }
       }
    }
    
    // ----------------------------------------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------------------------------------



    public function getAllReservations()
    {
        try {
            $model = new ReservationModel();
            $reservation = $model->getAllReservations();
            if(($reservation ?? [])){
                echo $this->response->sendResponse(200, true, 'Recipely reservation', $reservation);
                return true;
            }else{
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return false;
            }
        } catch (ReservationExceptions $e) {
            echo $this->response->sendResponse(400, false, $e->getMessage(), null);
            return false;
        }

    }

    public function getReservation($id)
    {
        try {

            if($this->checkValue($id, 'id', 'reservations') == true){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return;
            }

            $model = new ReservationModel();
            $reservation = $model->getReservation($id);
            if(is_array($reservation)){
                echo $this->response->sendResponse(200, true, 'Recipely reservation', $reservation);
                return true;
            }else{
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return false;
            }
        } catch (ReservationExceptions $e) {
            echo $this->response->sendResponse(400, false, $e->getMessage(), null);
            return false;
        }

    }

    public function getAllReservationsByUser($id)
    {
        try {

            if($this->checkValue($id, 'id', 'users') == true){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return;
            }

            $model = new ReservationModel();
            $reservation = $model->getAllReservationsByUser($id);
            if(is_array($reservation)){
                echo $this->response->sendResponse(200, true, 'User reservation', $reservation);
                return true;
            }else{
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return false;
            }
        } catch (ReservationExceptions $e) {
            echo $this->response->sendResponse(400, false, $e->getMessage(), null);
            return false;
        }

    }

    public function deleteReservation($id)
    {
        try {

            if($this->checkValue($id, 'id', 'reservations') == true){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return;
            }


            $model = new ReservationModel();
            $model->deleteReservation($id);

            echo $this->response->sendResponse(200, true, 'Reservation deleted', null);
            return true;
        } catch (ReservationExceptions $e) {
            echo $this->response->sendResponse(400, false, $e->getMessage(), null);
            return false;
        }
    }

    public function updateReservation($id, $reservation_data)
    {

        try {

            if($this->checkValue($id, 'id', 'reservations') == true){
                echo $this->response->sendResponse(400, false, $this->exception->notFound(), null);
                return;
            }

            $model = new ReservationModel();
            $reservation = $model->updateReservation($id, $reservation_data);

            echo $this->response->sendResponse(200, true, 'Reservation updated', $reservation);
            return true;
        } catch (ReservationExceptions $e) {
            echo $this->response->sendResponse(400, false, $e->getMessage(), null);
            return false;
        }
    }

}