<?php

namespace Recipely\Models\service;

use PDO;
use Recipely\Config\ConnectDb;
use Recipely\Controllers\service\Service;
use Recipely\Utils\ServiceExceptions;

class ServiceModel{
    public function insertService(Service $service){
        try{
            $pdo  = ConnectDb::getInstance();
            $sql_insert = "INSERT INTO services (id,type_service, price) VALUES (:id,:type_service, :price)";
     
            $stmt = $pdo->prepare($sql_insert);

            $params = [
                'id' => $service->toArray()['id'],
                'type_service' => $service->toArray()['type_service'],
                'price' => $service->toArray()['price']
            ];
            $stmt->execute($params);
            ConnectDb::closeConnexion();
            return $service->toArray();
        } catch(ServiceExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notCreated();
        }
    }

    public function getService($id){
        try{
            $pdo  = ConnectDb::getInstance();
            $sql_select = "SELECT id,type_service,price FROM services WHERE id = :id";
            $stmt = $pdo->prepare($sql_select);

            $stmt->execute( [
                'id' => $id
            ]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            ConnectDb::closeConnexion();
            return $service;
        } catch(ServiceExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function getAllServices(){
        try{
            $pdo  = ConnectDb::getInstance();
            $sql_select = "SELECT id,type_service,price FROM services";
            $stmt = $pdo->prepare($sql_select);

            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ConnectDb::closeConnexion();
            return $services;
        } catch(ServiceExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notFound();
        }
    }

    public function updateService($service_id,$service_data){
        $service_data = json_decode($service_data,true);

        try{
            $pdo = ConnectDb::getInstance();
            $checkId = $this->checkId($service_id,'id','services');

            if($checkId){
                $pdo->beginTransaction();
                $params = [
                ':id' => $service_id
                ];

                $updateService = [];
                foreach($service_data as $key => $value){
                    if($key != 'id'){
                        $updateService[] = "$key = :$key";
                        $params[":$key"] = $value;
                    }
                }

                $sql_update = "UPDATE services SET ".implode(',',$updateService)." WHERE id = :id";
                $stmt = $pdo->prepare($sql_update);
                $stmt->execute($params);

                $pdo->commit();
                ConnectDb::closeConnexion();
                return $service_data;
            } else{
                ConnectDb::closeConnexion();
                return throw new ServiceExceptions('Service not found');
            }
        } catch(ServiceExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notUpdated();
        }
    }
    
    public function deleteService($service_id){
        try{
            $pdo = ConnectDb::getInstance();
            $checkId = $this->checkId($service_id,'id','services');

            if($checkId){
                $pdo->beginTransaction();
                $sql_delete = "DELETE FROM services WHERE id = :id";
                $stmt = $pdo->prepare($sql_delete);
                $stmt->execute(['id' => $service_id]);

                $pdo->commit();
                ConnectDb::closeConnexion();
                return throw new ServiceExceptions('Service deleted');
            } else{
                ConnectDb::closeConnexion();
                return throw new ServiceExceptions('Service not found');
            }
        } catch(ServiceExceptions $e){
            ConnectDb::closeConnexion();
            return $e->notDeleted();
        }
    }


    public function checkId($value, $column, $table){
        $pdo = ConnectDb::getInstance();
        $stmt = $pdo->prepare("SELECT $column FROM $table WHERE $column = :value");
        $stmt->execute(['value' => $value]);

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
        ConnectDb::closeConnexion();

        if($fetch){
            return true;
        } else{
            return false;
        }
    }
}