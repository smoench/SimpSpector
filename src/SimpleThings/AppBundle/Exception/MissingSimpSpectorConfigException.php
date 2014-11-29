<?php
/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */

namespace SimpleThings\AppBundle\Exception;

use Exception;

class MissingSimpSpectorConfigException extends \Exception
{
    public function __construct()
    {
        parent::__construct("missing simpspector.yml");
    }
}
