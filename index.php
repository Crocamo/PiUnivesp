<?php

use WilliamCosta\DotEnv\Environment;

require __DIR__.'/includes/app.php';

use \App\Http\Router;

// INICIA O ROUTER
$obRouter = new Router(URL);

//INCLUE AS ROTAS DE PAGINAS.
include __DIR__.'/routes/pages.php';

//INCLUE AS ROTAS DO PAINEL.
include __DIR__.'/routes/admin.php';

//INCLUE AS ROTAS DO api.
include __DIR__.'/routes/api.php';

//IMPRIME O RESPONSE DA ROTA
$obRouter->run()
         ->sendResponse();

/*
    echo "<pre>";      
    print_r($);      
    echo "</pre>";exit;         
 */
