<?php

namespace Backend\Modules\NavigationBlock\Ajax;

/**
 * Returns alias template path
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use Backend\Modules\NavigationBlock\Engine\Model;

class GeneratePath extends BackendBaseAJAXAction
{
    /**
     * Execute the action
     */
    public function execute()
    {
        // call parent, this will probably add some general CSS/JS or other required files
        parent::execute();

        // get parameters
        $alias = \SpoonFilter::getPostValue('alias', null, '', 'string');

        if (preg_match('/^[-_a-z0-9]+$/', $alias) ) {
            $path = Model::getAliasTemplatePath($alias);
        } else {
            $path = '';
        }

        // output
        $this->output(self::OK, $path);
    }
}
