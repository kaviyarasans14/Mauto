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

    public function updatePaymentStatus($orderid, $paymentid, $status)
    {
        $paymenthistory     = $this->findBy(['orderid' => $orderid]);
        if (count($paymenthistory) > 0) {
            $payment=$paymenthistory[0];
            $payment->setPaymentID($paymentid);
            $payment->setPaymentStatus($status);
            $this->saveEntity($payment);
        }
    }
}
