<?php   

use \App\Http\Response;
use \App\Controller\Api;

//ROTA RAIZ DA API
$obRouter->get('/api/v1',[
    function($request){
        return new Response(200,Api\Api::getDetails($request));
    }
]);