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
class ModelValidationException extends MultipleExceptions {

    /**
     * ModelValidationException constructor.
     * @param array|\Illuminate\Support\MessageBag|string $messages
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($messages, $code = 0, Exception $previous = null) {
        $code = 422;

        parent::__construct($messages, $code, $previous);
        return $this;
    }
}
