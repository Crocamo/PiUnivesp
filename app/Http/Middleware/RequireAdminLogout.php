<?php

namespace App\Http\Middleware;

use \App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogout{

    /**
     * Método responsável por executar o middlewares
     * @param Request $request
     * @param Clousure next
     * @return Response
     */
    public function handle($request, $next){
        //VERIFICA SE O USUÁRIO ESTA LOGADO
        if(SessionAdminLogin::isLogged()){
           $request->getRouter()->redirect('/admin');
        }
       
        //CONTINUA A EXECUÇÃO
        return $next($request); 
    }
}