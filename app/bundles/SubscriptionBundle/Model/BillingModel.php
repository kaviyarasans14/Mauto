<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Model;

use Mautic\SubscriptionBundle\Entity\Billing;
use Mautic\CoreBundle\Model\FormModel;

/**
 * Class BillingModel.
 */
class BillingModel extends FormModel
{
    /**
     * {@inheritdoc}
     *
     * @return \Mautic\SubscriptionBundle\Entity\BillingRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticSubscriptionBundle:Billing');
    }

    /**
     * Get a specific entity or generate a new one if id is empty.
     *
     * @param $id
     *
     * @return null|Billing
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new Billing();
        }

        $entity = parent::getEntity($id);

        return $entity;
    }

    public function getCurrentUser()
    {
        return $this->userHelper->getUser();
    }

    public function createForm($entity, $formFactory, $action = null, $options = [])
    {
        if (!$entity instanceof Billing) {
            throw new MethodNotAllowedHttpException(['Billing'], 'Entity must be of class Billing()');
        }
        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create('billinginfo', $entity, $options);
    }
}
