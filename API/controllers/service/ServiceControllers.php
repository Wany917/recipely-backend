<?php

namespace Recipely\Controllers\service;

use Exception;
use Recipely\Lib\Response;
use Recipely\Lib\Header;
use Recipely\Auth\Authenticator;
use Recipely\Controllers\service\Service;
use Recipely\Models\service\ServiceModel;
use Recipely\Utils\ServiceExceptions;

class ServiceControllers{

    private Response $response;
    private Header $headers;
    private ServiceExceptions $exception;
    private Authenticator $authenticator;

    public function __construct(){
        $this->headers = new Header();
        $this->response = Response::create();
        $this->exception = new ServiceExceptions();    
        $this->authenticator = new Authenticator();
    }
    
    private Int $id;
    private String $type_service;
    private Float $price;

    private Array $errors = [];

    public function createService($service_data){
        $this->checkData($service_data);
        $this->id = $this->generateId();
        if(sizeof($this->errors) == 0){
            $service = new Service(
                $this->id,
                $this->type_service,
                $this->price
            );

            $service_model = new ServiceModel();
            $service_result = $service_model->insertService($service);


            echo $this->response->sendResponse(201, true, 'Service created succesfully',  $service_result);
            return true;
        } else{
            echo $this->response->sendResponse(400, false, 'Error while creating service', $this->errors);
            return false;
        }
    }

    public function checkData($data){
        foreach($data as $key => $value){
            if($key == 'type_service'){
                $this->checkTypeService($value);
            } else if($key == 'price'){
                $this->checkPrice($value);
            }
        }
    }

    public function checkTypeService($type_service){
        if($type_service == ''){
            $this->errors[] = 'Type service is required';
        } else if(strlen($type_service) > 50){
            $this->errors[] = 'Type service must be less than 50 characters';
        } else{
            $this->type_service = $type_service;
        }
    }

    public function checkPrice($price){
        if($price == ''){
            $this->errors[] = 'Price is required';
        } else if(!is_numeric($price)){
            $this->errors[] = 'Price must be a number';
        } else{
            $this->price = $price;
        }
    }

    private function generateId(){
        $id = substr(uniqid(), -5);
        return intval($id);
    }
    

    //-------------------------------------------------------------HTTP METHOD-------------------------------------------------------------------------------

    public function getService($id){
        try{
            $service_model = new ServiceModel();
            $service = $service_model->getService($id);

            if($service == null){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            } else{
                echo $this->response->sendResponse(200, true, 'Service found', $service);
            }
            return true;
        } catch(ServiceExceptions $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function getAllServices(){
        try{
            $service_model = new ServiceModel();
            $services = $service_model->getAllServices();

            if($services == null){
                echo $this->response->sendResponse(404, false, $this->exception->notFound(), null);
            } else{
                echo $this->response->sendResponse(200, true, 'Service found', $services);
            }
            return true;
        } catch(Exception $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
            return false;
        }
    }

    public function updateService($service_id,$service_data){
        try{
            $model = new ServiceModel;
            $service = $model->updateService($service_id,$service_data);

            echo $this->response->sendResponse(200, true, 'Service updated', $service);            
        } catch(Exception $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
        }
    }


    public function deleteService($id){
        try{
            $model =  new ServiceModel();
            $service = $model->deleteService($id);

            echo $this->response->sendResponse(200, true, 'Service deleted', null);
        } catch (Exception $e){
            echo $this->response->sendResponse(500, false, $e->getMessage(), null);
        }
    }


}