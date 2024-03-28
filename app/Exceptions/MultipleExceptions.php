<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 18:38
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

/**
 * Class MultipleExceptions
 * @package App\Exceptions
 */
class MultipleExceptions extends Exception {
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * MultipleExceptions constructor.
     * @param string|array|MessageBag $messages
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($messages, $code = 0, Exception $previous = null) {
        if($messages instanceof MessageBag) {
            /** @var $messages MessageBag */
            $this->messages = $messages->all();
        } elseif(is_array($messages)) {
            $this->messages = $messages;
            $messages = [];

            foreach($this->messages as $field=>$field_messages) {
                if(is_array($field_messages)) {
                    $messages = array_merge($messages, $field_messages);
                } elseif(is_string($field_messages)) {
                    $messages[] = $field_messages;
                }
            }

            $this->messages = $messages;
        } else {
            $this->messages = [$messages];
        }

        parent::__construct($this->__toString(), $code, $previous);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() {
        return implode("\n", $this->messages);
    }

    /**
     * @return array
     */
    public function getMessages() {
        return $this->messages;
    }
}
