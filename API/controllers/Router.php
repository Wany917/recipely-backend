<?php
namespace Recipely\Controllers;
class Router {
    private $routes = [];

    public function addRoute($method, $url, $handler){
        $url = $url . '/?';
        $this->routes[] = [
            'method' => strtoupper($method),
            'url' => $url,
            'handler' => $handler
        ];
    }
    

    public function handleRequest($method, $url) {
        foreach($this->routes as $route){
            // Match the URL pattern
            $urlPattern = preg_replace('~:([\w]+)~', '(?P<\1>[\w\-]+)', $route['url']);
            $urlPattern = str_replace('/', '\/', $urlPattern);
            $urlPattern = '/^' . $urlPattern . '$/';
            
            $matches = [];

            if($route['method'] == strtoupper($method) && preg_match($urlPattern, $url, $matches)){
                $handler = $route['handler'];
                // Run the handler with parameters
                return $handler($matches);
            }
        }
        return http_response_code(404);
    }   
}
