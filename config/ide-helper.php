<?php
/**
 * Created by Antony Repin
 * Date: 24.04.2017
 * Time: 0:52
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Filename & Format
    |--------------------------------------------------------------------------
    |
    | The default filename (without extension) and the format (php or json)
    |
    */

    'filename'  => '_ide_helper',
    'format'    => 'php',


    /*
     * If you need phpDocs support for Fluent methods in migration, for example
     */
    'include_fluent' => true,

    /*
    |--------------------------------------------------------------------------
    | Helper files to include
    |--------------------------------------------------------------------------
    |
    | Include helper files. By default not included, but can be toggled with the
    | -- helpers (-H) option. Extra helper files can be included.
    |
    */

    'include_helpers' => false,

    'helper_files' => array(
        base_path().'/vendor/laravel/framework/src/Illuminate/Support/helpers.php',
    ),

    /*
    |--------------------------------------------------------------------------
    | Model locations to include
    |--------------------------------------------------------------------------
    |
    | Define in which directories the ide-helper:models command should look
    | for models.
    |
    */

    'model_locations' => array(
        'app',
    ),

    /*
    |--------------------------------------------------------------------------
    | Extra classes
    |--------------------------------------------------------------------------
    |
    | These implementations are not really extended, but called with magic functions
    |
    */

    'extra' => array(
        'Eloquent' => array('Illuminate\Database\Eloquent\Builder', 'Illuminate\Database\Query\Builder'),
        'Session' => array('Illuminate\Session\Store'),
    ),

    'magic' => array(
        'Log' => array(
            'debug'     => 'Monolog\Logger::addDebug',
            'info'      => 'Monolog\Logger::addInfo',
            'notice'    => 'Monolog\Logger::addNotice',
            'warning'   => 'Monolog\Logger::addWarning',
            'error'     => 'Monolog\Logger::addError',
            'critical'  => 'Monolog\Logger::addCritical',
            'alert'     => 'Monolog\Logger::addAlert',
            'emergency' => 'Monolog\Logger::addEmergency',
        )
    ),

    /*
    |--------------------------------------------------------------------------
    | Interface implementations
    |--------------------------------------------------------------------------
    |
    | These interfaces will be replaced with the implementing class. Some interfaces
    | are detected by the helpers, others can be listed below.
    |
    */

    'interfaces' => [],

    /*
     |--------------------------------------------------------------------------
     | Support for camel cased models
     |--------------------------------------------------------------------------
     |
     | There are some Laravel packages (such as Eloquence) that allow for accessing
     | Eloquent model properties via camel case, instead of snake case.
     |
     | Enabling this option will support these packages by saving all model
     | properties as camel case, instead of snake case.
     |
     | For example, normally you would see this:
     |
     |  * @property \Carbon\Carbon $created_at
     |  * @property \Carbon\Carbon $updated_at
     |
     | With this enabled, the properties will be this:
     |
     |  * @property \Carbon\Carbon $createdAt
     |  * @property \Carbon\Carbon $updatedAt
     |
     | Note, it is currently an all-or-nothing option.
     |
     */
    'model_camel_case_properties' => false,

];
