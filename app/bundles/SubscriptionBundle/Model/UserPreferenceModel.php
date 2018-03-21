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
use Mautic\SubscriptionBundle\Entity\UserPreference;

/**
 * Class UserPreferenceModel.
 */
class UserPreferenceModel extends FormModel
{
    /**
     * {@inheritdoc}
     *
     * @return \Mautic\SubscriptionBundle\Entity\UserPreferenceRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticSubscriptionBundle:UserPreference');
    }

    /**
     * Get a specific entity or generate a new one if id is empty.
     *
     * @param $id
     *
     * @return null|UserPreference
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new UserPreference();
        }

        $entity = parent::getEntity($id);

        return $entity;
    }

    public function getCurrentUser()
    {
        return $this->userHelper->getUser();
    }
}
