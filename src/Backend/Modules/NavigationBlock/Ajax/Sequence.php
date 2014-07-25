<?php

namespace Backend\Modules\NavigationBlock\Ajax;

/**
 * Alters the sequence of Navigation Block articles
 *
 * @author Bart Lagerweij <bart@webleads.nl>
 */

use Backend\Core\Engine\Base\AjaxAction as BackendBaseAJAXAction;
use SpoonFilter;

use Backend\Modules\NavigationBlock\Engine\Model as BackendNavigationBlockModel;

class Sequence extends BackendBaseAJAXAction
{
    public function execute()
    {
        parent::execute();

        // get parameters
        $newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

        // list id
        $ids = (array)explode(',', rtrim($newIdSequence, ','));

        // loop id's and set new sequence
        foreach ($ids as $i => $id) {
            $item['id'] = $id;
            $item['sequence'] = $i + 1;

            // update sequence
            if (BackendNavigationBlockModel::exists($id)) {
                BackendNavigationBlockModel::update($item);
            }
        }

        // success output
        $this->output(self::OK, null, 'sequence updated');
    }
}
