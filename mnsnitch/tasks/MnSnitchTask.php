<?php
/**
 * MN Snitch plugin for Craft CMS
 *
 * MnSnitch Task
 *
 * @author    Marion Newlevant
 * @copyright Copyright (c) 2016 Marion Newlevant
 * @link      http://marion.newlevant.com
 * @package   MnSnitch
 * @since     1.0.0
 */

namespace Craft;

class MnSnitchTask extends BaseTask
{
    /**
     * @access protected
     * @return array
     */

    protected function defineSettings()
    {
        return array(
            'someSetting' => AttributeType::String,
        );
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'MnSnitch Tasks';
    }

    /**
     * @return int
     */
    public function getTotalSteps()
    {
        return 1;
    }

    /**
     * @param int $step
     * @return bool
     */
    public function runStep($step)
    {
        return true;
    }
}
