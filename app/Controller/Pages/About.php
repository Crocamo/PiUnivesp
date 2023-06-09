<?php 

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\model\Entity\Organization;

class about extends Page{

    /**
     * Metodo responsável por retornar o conteúdo (view) da nossa página Sobre
     * @return string
     */
    public static function getAbout(){
        //ORGANIZAÇÃO
        $obOrganization = new Organization;

        //VIEW DA HOME
        $content= View::render('pages/about', [
            'name' => $obOrganization->name,
            'description' => $obOrganization->description,
            'site' => $obOrganization->site
        ]);

        // RETORNA A VIEW DA PAGINA
        return parent::getPage('SOBRE > Projeto Integrador', $content);

    }
}
?>