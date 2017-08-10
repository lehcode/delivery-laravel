<?php
/**
 * Created by Antony Repin
 * Date: 28.04.2017
 * Time: 17:52
 */

namespace App\Exceptions;

use Exception;

/**
 * Class ModelValidationException
 * @package App\Exceptions
 */
class RequestValidationException extends MultipleExceptions {

    /**
     * ModelValidationException constructor.
     * @param array|\Illuminate\Support\MessageBag|string $messages
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($messages, $code = 403, Exception $previous = null) {
        parent::__construct($messages, $code, $previous);
        return $this;
    }
}
