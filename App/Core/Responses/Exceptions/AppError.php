<?php

namespace App\Core\Responses\Exceptions;

class AppError extends \DomainException
{
    public function __construct($mensagem = '')
    {
        parent::__construct($mensagem);
    }
}
