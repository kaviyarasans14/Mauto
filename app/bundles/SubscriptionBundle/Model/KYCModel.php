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

use Mautic\CoreBundle\Model\FormModel;
use Mautic\SubscriptionBundle\Entity\KYC;

/**
 * Class KYCModel.
 */
class KYCModel extends FormModel
{
    /**
     * {@inheritdoc}
     *
     * @return \Mautic\SubscriptionBundle\Entity\KYCRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticSubscriptionBundle:KYC');
    }

    /**
     * Get a specific entity or generate a new one if id is empty.
     *
     * @param $id
     *
     * @return null|KYC
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new KYC();
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
        if (!$entity instanceof KYC) {
            throw new MethodNotAllowedHttpException(['KYC'], 'Entity must be of class KYC()');
        }
        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create('kycinfo', $entity, $options);
    }
}
