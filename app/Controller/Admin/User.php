<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;


class User extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de usuários para a página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUserItens($request, &$obPagination)
    {
        //USUÁRIOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        //INSTANCIA DE PAGINAÇÃO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        //RESULTADOS DA PÁGINA
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obUser = $results->fetchObject(EntityUser::class)) {

            $itens .= View::render('admin/modules/users/item', [
                'id'    => $obUser->id,
                'nome'  => $obUser->nome,
                'email' => $obUser->email
            ]);
        }

        //RETORNA OS USUÁRIOSS
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de usuários
     * @param Request
     * @return string
     */
    public static function getUsers($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/users/index', [
            'itens'      => self::getUserItens($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Usuários > Projeto Integrador', $content, 'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuários
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        //CONTEÚDO DA HOME
        $content = View::render('admin/modules/users/form', [
            'title'     => 'Cadastrar usuário',
            'nome'      => '',
            'email'     => '',
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Cadastrar usuário > Projeto Integrador', $content, 'users');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $nome  = $postVars['nome'] ?? '';
        $senha  = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUser =  EntityUser::getUserByEmail($email);
        if ($obUser instanceof EntityUser) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/new?status=duplicated');
        }

        //NOVA INSTANCIA DE USUÁRIOS
        $obUser         = new EntityUser;
        $obUser->nome   = $nome;
        $obUser->email  = $email;
        $obUser->senha  = password_hash($senha,PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=created');
    }

    /**
     * Método responsável por retornar a mensagem de status
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //  QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGEM DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário deletado com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário!');
                break;

        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getEditUser($request, $id)
    {
        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/Users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'     => 'Editar usuário',
            'nome'      => $obUser->nome,
            'email'     => $obUser->email,            
            'status'    => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Editar usuário > Projeto Integrador', $content, 'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setEditUser($request, $id)
    {
       // OBTÉM O USUÁRIOS DO BANCO DE DADOS
       $obUser = EntityUser::getUserById($id);

       //VALIDA A INSTANCIA
       if (!$obUser instanceof EntityUser) {
           $request->getRouter()->redirect('/admin/Users');
       }
 
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $nome  = $postVars['nome'] ?? '';
        $senha  = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUserEmail =  EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            //REDIRECIONA O USUÁRIO
            $request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
        }

        //ATUALIZA A INSTANCIA
        $obUser->nome   = $nome;
        $obUser->email  = $email;
        $obUser->senha  = password_hash($senha,PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users/' . $obUser->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function getDeleteUser($request, $id)
    {
        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/Users');
        }

        //CONTEÚDO DO FORMULÁRIO
        $content = View::render('admin/modules/users/delete', [
            'nome'      => $obUser->nome,
            'email'  => $obUser->email
        ]);

        
        //RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Excluir USUÁRIO > Projeto Integrador', $content, 'users');
    }
    
    /**
     * Método responsável por excluir um usuário
     * @param Request $request
     * @param interger $id
     * @return string
     */
    public static function setDeleteUser($request, $id)
    {
        // OBTÉM O USUÁRIOS DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin/Users');
        }

        //EXCLUI O USUÁRIOS
        $obUser->excluir();

        //REDIRECIONA O USUÁRIO
        $request->getRouter()->redirect('/admin/users?status=deleted');
    }
}
