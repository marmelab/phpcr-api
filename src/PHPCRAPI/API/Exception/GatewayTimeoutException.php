<?php

/*
 * This file is part of the marmelab/phpcr-api package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPCRAPI\API\Exception;

/**
 * The GatewayTimeoutException is thrown when a repository is unavailable
 *
 * @api
 */

class GatewayTimeoutException extends \Exception implements ExceptionInterface
{
    public function __construct($message)
    {
        parent::__construct($message, 504);
    }
}
