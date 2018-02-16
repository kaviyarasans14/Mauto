<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Event;

use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class LeadEvent.
 */
class LeadMergeEvent extends Event
{
    private $victor;

    private $loser;

    private $details;

    /**
     * @param Lead $victor
     * @param Lead $loser
     * @param $details
     */
    public function __construct(Lead $victor, Lead $loser, $details)
    {
        $this->victor   = $victor;
        $this->loser    = $loser;
        $this->details  = $details;
    }

    /**
     * Returns the victor (loser merges into the victor).
     *
     * @return Lead
     */
    public function getVictor()
    {
        return $this->victor;
    }

    /**
     * Returns the loser (loser merges into the victor).
     *
     * @param Lead $lead
     */
    public function getLoser()
    {
        return $this->loser;
    }

    /**
     * Returns the loser (loser merges into the victor).
     *
     * @param Lead $lead
     */
    public function getLoserDetails()
    {
        return $this->details;
    }
}
