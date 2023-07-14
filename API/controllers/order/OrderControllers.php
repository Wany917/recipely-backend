<?php 

namespace Recipely\Controllers\order;

use Exception;
use PDO;
use Recipely\Lib\Response;
use Recipely\Lib\JsonRes;
use Recipely\Lib\Header;
use Recipely\Config\ConnectDb;
use Recipely\Auth\Authenticator;
use Recipely\Controllers\order\Order;
use Recipely\Models\order\OrderModel;
use Recipely\Utils\OrderExceptions;

class OrderControllers {

    private Response $response;
    private Header $headers;
    private OrderExceptions $exception;
    private Authenticator $authenticator;
    
    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new OrderExceptions;    
        $this->authenticator = new Authenticator();
    }

    private Int $id;
    private String $id_client;
    private String $id_service;
    private Array $errors = [];

    public function createOrder($id_client, $id_service){
        if($this->checkValue($id_client, 'id', 'users') && $this->checkValue($id_service, 'id', 'services')){
            
            $this->id = $this->generateId();
            $this->id_client = $id_client;
            $this->id_service = $id_service;

            if(sizeof($this->errors) == 0){
                $order = new Order(
                    $this->id,
                    $this->id_client,
                    $this->id_service
                );

                $orderModel = new OrderModel();
                $orderResult = $orderModel->insertOrder($order);
                
                echo $this->response->sendResponse(201, true, 'Order created succesfully', $orderResult);
                return true;
            } else {
                echo $this->response->sendResponse(400, false , 'Error while creating order', $this->errors);
                return false;
            }
        }else{
            echo $this->response->sendResponse(500, false, 'Error while creating order', $this->errors);
            return false;
        }
    }

    /*private function checkData($data){
        foreach($data as $key => $value){
            if($key == 'id_client'){
                $this->checkValueClient($value);
            } else if($key == 'id_service'){
                $this->checkValueService($value);
            }
        }
    }*/

    private function checkValue($value, $column, $table): bool {
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    
        $fetch = $stmt->fetch();
        ConnectDb::closeConnexion();
    
        if ($fetch) {
            return true;
        }
        $this->errors[] = 'Invalid ID provided';
        return false;
    }

    private function generateId(){
        $id = uniqid();
        return intval($id);
    }
    
    // ----------------------------------------------------------------------------------------------------------------

    // ----------------------------------------------------------------------------------------------------------------

    public function updateOrder($id, $order_data){
        try{
            $orderModel = new OrderModel();
            $order = $orderModel->updateOrder($id, $order_data);

            if(!empty($order)){
                echo $this->response->sendResponse(200, true, 'Order updated succesfully', null);
            }else{
                echo $this->response->sendResponse(400, false, $this->exception->notUpdated(), null);
            }
            return true;
        }catch (OrderExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function deleteOrder($id){
        try{
            $orderModel = new OrderModel();
            $order = $orderModel->deleteOrder($id);

            if($order != true){
                echo $this->response->sendResponse(400, false, $this->exception->notDeleted(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Order updated succesfully', null);
            }
            return true;
        }catch (OrderExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getOrder($id){
        try{
            $orderModel = new OrderModel();
            $order = $orderModel->getOrder($id);

            if(empty($order)){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            }else{
                echo $this->response->sendResponse(200, true, 'Recipely order', $order);
            }
            return true;
        }catch (OrderExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getAllOrders(){
        try{
            $orderModel = new OrderModel();
            $order = $orderModel->getAllOrders();
            if(is_array($order)){
                echo $this->response->sendResponse(200, true, 'Recipely order', $order);
                return true;
            }
        }catch (OrderExceptions $e){
            echo $this->response->sendResponse(404, false, $e->getMessage(), null);
            return false;
        }
    }


}

