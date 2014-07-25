<?php

namespace Frontend\Modules\NavigationBlock;

/**
 * This is the configuration-object for the Navigation Block module
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/**
 * This is the configuration-object
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Config extends FrontendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Detail';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();
}
