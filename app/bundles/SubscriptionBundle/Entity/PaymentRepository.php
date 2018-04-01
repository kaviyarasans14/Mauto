<?php

/*
 * @copyright   2014 Mautic Contributorcomp. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

/**
 * Class PaymentRepository.
 */
class PaymentRepository extends CommonRepository
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return 'ph';
    }
}
