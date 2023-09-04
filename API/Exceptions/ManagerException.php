<?php

namespace API\Exceptions;

use Core\Helpers\RequestHelper\Response;
use Exception;

abstract class ManagerException extends Exception
{
    public readonly ?array $details;

    public function __construct(?Response $response = null)
    {
        $this->details = !is_null($response) && is_array($response->getDecodedJsonBody()) ? $response->getDecodedJsonBody() : null;
        parent::__construct();
    }
}
