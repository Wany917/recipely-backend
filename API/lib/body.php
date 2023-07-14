<?php
function getBody(){
    $body = json_decode(file_get_contents('php://input'),true);
    return $body;
}