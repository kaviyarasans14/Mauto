<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SubscriptionBundle\Command;

use Mautic\CoreBundle\Command\ModeratedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePaymentCommand extends ModeratedCommand
{
    protected function configure()
    {
        $this
            ->setName('le:payment:update')
            ->setAliases(['le:payment:update'])
            ->setDescription('Update payment based on last payment validity end')
            ->addOption('--domain', '-d', InputOption::VALUE_REQUIRED, 'To load domain specific configuration', '');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $domain    = $input->getOption('domain');
            if (!$this->checkRunStatus($input, $output, $domain)) {
                return 0;
            }
            $container  = $this->getContainer();
            // $translator = $container->get('translator');
            // $translator->trans('mautic.campaign.rebuild.leads_affected', ['%leads%' => $processed])
            $paymentrepository  =$container->get('le.subscription.repository.payment');
            $lastpayment        =$paymentrepository->getLastPayment();
            if ($lastpayment != null) {
                $paymenthelper     =$container->get('le.helper.payment');
                $licenseinfohelper = $container->get('mautic.helper.licenseinfo');
                $licenseinfo       =$licenseinfohelper->getLicenseEntity();
                if ($licenseinfo != null) {
                    $totalrecordcount =$licenseinfo->getTotalRecordCount();
                    $actualrecordcount=$licenseinfo->getActualRecordCount();
                    $validitytill     = $lastpayment->getValidityTill();
                    $currentdate      = date('Y-m-d');
                    $planname         = $lastpayment->getPlanName();
                    $planamount       = $lastpayment->getAmount();
                    $plancredits      =$lastpayment->getAfterCredits();
                    $stripecardrepo   = $container->get('le.subscription.repository.stripecard');
                    $stripecards      = $stripecardrepo->findAll();
                    $stripecard       = null;
                    if (sizeof($stripecards) > 0) {
                        $stripecard = $stripecards[0];
                    }
                    if ($stripecard != null) {
                        if (strtotime($validitytill) < strtotime($currentdate)) {
                            $output->writeln('<info>'.'Total Record Count:'.$totalrecordcount.'</info>');
                            $output->writeln('<info>'.'Actual Record Count:'.$actualrecordcount.'</info>');
                            if ($totalrecordcount < $actualrecordcount) {
                                $multiplx   =ceil($actualrecordcount / 10000);
                                $planamount =$planamount * $multiplx;
                                $plancredits=$plancredits * $multiplx;
                            }
                            $apikey=$container->get('mautic.helper.core_parameters')->getParameter('stripe_api_key');
                            \Stripe\Stripe::setApiKey($apikey);
                            $charges = \Stripe\Charge::create([
                                'amount'   => $planamount * 100, //100 cents = 1 dollar
                                'currency' => 'usd',
                                //"source" => $token, // obtained with Stripe.js
                                'customer'             => $stripecard->getCustomerID(),
                                'description'          => 'charge for leadsengage product purchase',
                                'capture'              => true,
                                'statement_descriptor' => 'leadsengage purchase',
                            ], [
                                'idempotency_key' => $paymenthelper->getUUIDv4(),
                            ]);
                            if (isset($charges)) {
                                $orderid         = uniqid();
                                $chargeid        = $charges->id;
                                $status          = $charges->status;
                                $failure_code    = $charges->failure_code;
                                $failure_message = $charges->failure_message;
                                if ($status == 'succeeded') {
                                    $validitytill=date('Y-m-d', strtotime($validitytill.' +1 months'));
                                    $paymentrepository->captureStripePayment($orderid, $chargeid, $planamount, $plancredits, $validitytill, $planname, null, null);
                                    $subsrepository          =$container->get('le.core.repository.subscription');
                                    $subsrepository->updateContactCredits($plancredits);
                                    $output->writeln('<info>'.'Plan Renewed Successfully'.'</info>');
                                    $output->writeln('<info>'.'Transaction ID:'.$chargeid.'</info>');
                                    $output->writeln('<info>'.'Amount($):'.$planamount.'</info>');
                                    $output->writeln('<info>'.'Contact Credits:'.$plancredits.'</info>');
                                } else {
                                    $output->writeln('<error>'.'Plan renewed failed due to some technical issues.'.'</error>');
                                    $output->writeln('<error>'.'Failure Code:'.$failure_code.'</error>');
                                    $output->writeln('<error>'.'Failure Message:'.$failure_message.'</error>');
                                }
                            } else {
                                $output->writeln('<error>'.'Plan renewed failed due to some technical issues.'.'</error>');
                            }
                        } else {
                            $output->writeln('<info>'."Plan validity available upto $validitytill".'</info>');
                        }
                    } else {
                        $output->writeln('<error>'.'Customer Credit Card details not found.'.'</error>');
                    }
                } else {
                    $output->writeln('<error>'.'License info details not found.'.'</error>');
                }
            } else {
                $output->writeln('<error>'.'Last payment details not found.'.'</error>');
//                $output->writeln(
//                    '<comment>'."LeadsEngage Comment".'</comment>'."\n"
//                );
            }
            $this->completeRun();

            return 0;
        } catch (\Stripe\Error\Card $e) {
            // Since it's a decline, \Stripe\Error\Card will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];

            //  print('Status is:' . $e->getHttpStatus() . "\n");
            // print('Type is:' . $err['type'] . "\n");
            //print('Code is:' . $err['code'] . "\n");
            // param is '' in this case
            //  print('Param is:' . $err['param'] . "\n");
            // print('Message is:' . $err['message'] . "\n");
            $errormsg=$err['message'];
        } catch (\Stripe\Error\RateLimit $e) {
            $errormsg= 'Too many requests made to the API too quickly';
            // Too many requests made to the API too quickly
        } catch (\Stripe\Error\InvalidRequest $e) {
            $errormsg= "Invalid parameters were supplied to Stripe's API->".$e->getMessage();
            // Invalid parameters were supplied to Stripe's API
        } catch (\Stripe\Error\Authentication $e) {
            $errormsg= "Authentication with Stripe's API failed";
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
        } catch (\Stripe\Error\ApiConnection $e) {
            $errormsg= 'Network communication with Stripe failed';
            // Network communication with Stripe failed
        } catch (\Stripe\Error\Base $e) {
            $errormsg= 'Display a very generic error to the user, and maybe send->'.$e->getMessage();
            // Display a very generic error to the user, and maybe send
            // yourself an email
        } catch (\Exception $e) {
            $errormsg= 'General Error:'.$e->getMessage();
            // Something else happened, completely unrelated to Stripe
        }
        if ($errormsg != '') {
            $output->writeln('<error>'.$errormsg.'</error>');
            echo 'exception->'.$errormsg."\n";

            return 0;
        }
    }
}
