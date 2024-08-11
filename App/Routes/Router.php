<?php

namespace App\Routes;
use App\Core\AbstractRouter;
use App\Http\Controller\UserController;

class Router extends AbstractRouter
{
    // Apenas para demonstração
    public function __construct() 
    {
        $this->addRoute('GET', '/users', UserController::class, 'index');
    }
}
