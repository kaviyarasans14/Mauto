<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Model;

use Mautic\CoreBundle\Entity\Account;

/**
 * Class AccountInfoModel.
 */
class AccountInfoModel extends FormModel
{
    /**
     * {@inheritdoc}
     *
     * @return \Mautic\CoreBundle\Entity\AccountRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticCoreBundle:Account');
    }

    /**
     * Get a specific entity or generate a new one if id is empty.
     *
     * @param $id
     *
     * @return null|Account
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new Account();
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
        if (!$entity instanceof Account) {
            throw new MethodNotAllowedHttpException(['Account'], 'Entity must be of class Account()');
        }
        if (!empty($action)) {
            $options['action'] = $action;
        }

        return $formFactory->create('accountinfo', $entity, $options);
    }
}
