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

    public function getLastPayment()
    {
        $paymenthistory=$this->findBy([], ['createdOn'=> 'DESC'], 1, 0);
        if (count($paymenthistory) > 0) {
            $payment=$paymenthistory[0];

            return $payment;
        } else {
            return null;
        }
    }

    public function captureStripePayment($orderid, $chargeid, $amount, $plancredits, $validitytill, $planname, $createdby, $createdbyuser)
    {
        $paymenthistory=new PaymentHistory();
        $paymenthistory->setOrderID($orderid);
        $paymenthistory->setPaymentID($chargeid);
        $paymenthistory->setPaymentStatus('Paid');
        $paymenthistory->setProvider('stripe');
        $paymenthistory->setCurrency('$');
        $paymenthistory->setAmount($amount);
        $paymenthistory->setBeforeCredits($plancredits);
        $paymenthistory->setAddedCredits($plancredits);
        $paymenthistory->setAfterCredits($plancredits);
        $paymenthistory->setValidityTill($validitytill);
        $paymenthistory->setPlanName($planname);
        $paymenthistory->setPlanLabel($planname == 'viaaws' ? 'Via AWS' : 'Via LeadsEngage');
        $paymenthistory->setcreatedBy($createdby);
        $paymenthistory->setcreatedByUser($createdbyuser);
        $paymenthistory->setcreatedOn(new \DateTime());
        $paymenthistory->setNetamount($amount);
        $paymenthistory->setTaxamount(0);
        $this->saveEntity($paymenthistory);
    }
}
