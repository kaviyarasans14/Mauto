<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Scheduler\Builder;

use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\ReportBundle\Scheduler\Exception\InvalidSchedulerException;
use Mautic\ReportBundle\Scheduler\Exception\NotSupportedScheduleTypeException;
use Mautic\ReportBundle\Scheduler\Factory\SchedulerTemplateFactory;
use Mautic\ReportBundle\Scheduler\SchedulerInterface;
use Recurr\Exception\InvalidWeekday;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

class SchedulerBuilder
{
    /** @var SchedulerTemplateFactory */
    private $schedulerTemplateFactory;

    /**
     * SchedulerBuilder constructor.
     *
     * @param SchedulerTemplateFactory $schedulerTemplateFactory
     */

    /** @var UserHelper */
    private $userHelper;

    public function __construct(UserHelper $userHelper, SchedulerTemplateFactory $schedulerTemplateFactory)
    {
        $this->userHelper               =$userHelper;
        $this->schedulerTemplateFactory = $schedulerTemplateFactory;
    }

    /**
     * @param SchedulerInterface $scheduler
     *
     * @return \Recurr\Recurrence[]|\Recurr\RecurrenceCollection
     *
     * @throws InvalidSchedulerException
     * @throws NotSupportedScheduleTypeException
     */
    public function getNextEvent(SchedulerInterface $scheduler)
    {
        return $this->getNextEvents($scheduler, 1);
    }

    /**
     * @param SchedulerInterface $scheduler
     * @param int                $count
     *
     * @return \Recurr\Recurrence[]|\Recurr\RecurrenceCollection
     *
     * @throws InvalidSchedulerException
     * @throws NotSupportedScheduleTypeException
     */
    public function getNextEvents(SchedulerInterface $scheduler, $count)
    {
        if (!$scheduler->isScheduled()) {
            throw new InvalidSchedulerException();
        }
        date_default_timezone_set('UTC');
        $schedulerTime    = $scheduler->getScheduleDate();
        list($hour, $min) = explode(':', $schedulerTime);
        $startDate        = (new \DateTime())->setTime($hour, $min)->modify('+1 day');
        $rule             = new Rule();

        $rule->setStartDate($startDate)
            ->setCount($count);
        $builder = $this->schedulerTemplateFactory->getBuilder($scheduler);

        try {
            $finalScheduler = $builder->build($rule, $scheduler);
            $transformer    = new ArrayTransformer();

            return $transformer->transform($finalScheduler);
        } catch (InvalidWeekday $e) {
            throw new InvalidSchedulerException();
        }
    }

    /**
     * @param SchedulerInterface $scheduler
     * @param int                $count
     *
     * @return \Recurr\Recurrence[]|\Recurr\RecurrenceCollection
     *
     * @throws InvalidSchedulerException
     * @throws NotSupportedScheduleTypeException
     */
    public function getPreviewEvents(SchedulerInterface $scheduler, $count)
    {
        if (!$scheduler->isScheduled()) {
            throw new InvalidSchedulerException();
        }
        $userTime     = $scheduler->getScheduleDate();
        $userTimeZone =$this->userHelper->getUser()->getTimezone();
        date_default_timezone_set($userTimeZone);
        list($hour, $min, $sec) = explode(':', $userTime);
        $startDate              = (new \DateTime())->setTime($hour, $min)->modify('+1 day');
        $rule                   = new Rule();
        $rule->setStartDate($startDate)
            ->setCount($count);
        $builder = $this->schedulerTemplateFactory->getBuilder($scheduler);
        try {
            $finalScheduler = $builder->build($rule, $scheduler);
            $transformer    = new ArrayTransformer();

            return $transformer->transform($finalScheduler);
        } catch (InvalidWeekday $e) {
            throw new InvalidSchedulerException();
        }
    }
}
