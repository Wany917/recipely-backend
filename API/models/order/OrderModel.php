<?php 

namespace Recipely\Models\order;

use PDO,PDOException, InvalidArgumentException;
use Recipely\Lib\JsonRes;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\order\Order;
use Recipely\Utils\OrderExceptions;

class OrderModel{

    public function insertOrder(Order $order){
        try{
           $pdo = ConnectDb::getInstance();
           $stmt = $pdo->prepare('INSERT INTO orders(id_client, id_service) VALUES (:id_client, :id_service)');


           $params = [
            'id_client' => $order->toArray()['id_client'],
            'id_service' => $order->toArray()['id_service']
           ];

           $stmt->execute($params);

           ConnectDb::closeConnexion();
           return $order->toArray();
        }catch (OrderExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notCreated();
        }
    }

    public function updateOrder($id, $order){
        try{
            $order = json_decode($order, true);

            if(json_last_error() !== JSON_ERROR_NONE){
                return throw new OrderExceptions('Invalid JSON provided');
            }

            if(!isset($id)){
                return throw new OrderExceptions('Invalid ID provided');
            }

            $pdo = ConnectDb::getInstance();

            $stmt = $pdo->prepare('SELECT id, id_client, id_service FROM orders where id = :id');

            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$result){
                return throw new OrderExceptions('Invalid ID provided');
            }

            $params = [':id' => $id];
            $updates = [];

            foreach($order as $key => $value){
                if($key !== 'id'){
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($updates)) {
                return throw new OrderExceptions('No fields provided to update');
            }else{
                $sql = "UPDATE orders SET " . implode(', ', $updates) . " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
            }
            ConnectDb::closeConnexion();
            return true;
        }catch (OrderExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();

        }
    }

    public function deleteOrder($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare('SELECT id FROM orders WHERE id = :id');

            $params = ['id' => $id];
            
            $stmt->execute($params);

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
                ConnectDb::closeConnexion();
                return throw new OrderExceptions('Order not found');
            }else{
                $sql = "DELETE FROM orders WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                ConnectDb::closeConnexion();
                return true;
            }

        }catch (OrderExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notDeleted();
        }
    }


    public function getOrder($id){
        try{
            $pdo = ConnectDb::getInstance();
            $stmt = $pdo->prepare('SELECT id, date_order, id_client, id_service FROM orders WHERE id = :id');
            
            $params = ['id' => $id];

            $stmt->execute($params);

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(empty($fetch)){
                return false;
            }else{
                ConnectDb::closeConnexion();
                return $fetch;
            }
        }catch (OrderExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllOrders(){
        try{
            $pdo = ConnectDb::getInstance();

            $stmt = $pdo->prepare('SELECT id, date_order, id_client, id_service FROM orders');
            $stmt->execute();

            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$fetch){
                return throw new OrderExceptions('Orders not found');
            }else{
                ConnectDb::closeConnexion();
                return $fetch;
            }

        }catch(InvalidArgumentException $e){
            ConnectDb::closeConnexion();
            return throw new OrderExceptions($e->getMessage());
        }
    }
}