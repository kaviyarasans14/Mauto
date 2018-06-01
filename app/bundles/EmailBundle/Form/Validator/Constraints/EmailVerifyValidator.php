<?php

/*
 * @copyright   2018 LeadsEngage Contributors. All rights reserved
 * @author      LeadsEngage
 *
 * @link        http://leadsengage.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Form\Validator\Constraints;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\EmailBundle\Entity\AwsVerifiedEmails;
use Mautic\EmailBundle\Helper\EmailValidator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailVerifyValidator extends ConstraintValidator
{
    /**
     * @var MauticFactory
     */
    private $factory;

    /**
     * @var EmailValidator
     */
    protected $emailValidator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(MauticFactory $factory, EmailValidator $emailValidator, TranslatorInterface $translator)
    {
        $this->factory        = $factory;
        $this->emailValidator = $emailValidator;
        $this->translator     = $translator;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value != '' && !preg_match('/^.+\@\S+\.\S+$/', $value)) {
            return;
        }
        /** @var \Mautic\CoreBundle\Configurator\Configurator $configurator */
        $configurator= $this->factory->get('mautic.configurator');

        $params         = $configurator->getParameters();
        $fromadress     = $params['mailer_from_email'];
        $transport      = $params['mailer_transport'];
        $newfromaddress = $value;
        if ($transport == 'mautic.transport.amazon' && $fromadress != $newfromaddress) {
            $emailuser     = $params['mailer_user'];
            $emailpassword = $params['mailer_password'];
            $region        = $params['mailer_amazon_region'];
            $message       = '';
            if ($emailpassword == '') {
                $message = $this->translator->trans('le.email.password.error');
            }
            /** @var \Mautic\EmailBundle\Model\EmailModel $emailModel */
            $emailModel       = $this->factory->getModel('email');
            $getAllEmailIds   =$emailModel->getAllEmailAddress();
            $verifiedemailRepo=$emailModel->getAwsVerifiedEmailsRepository();
            $verifiedEmails   = $this->emailValidator->getVerifiedEmailList($emailuser, $emailpassword, $region);
            /** @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper $routerHelper */
            $awscallbackurl = $this->factory->get('templating.helper.router')->url('mautic_mailer_transport_callback', ['transport' => 'amazon_api']);
            $isValidEmail   = $this->emailValidator->getVerifiedEmailAddressDetails($emailuser, $emailpassword, $region, $newfromaddress);
            $entity         = new AwsVerifiedEmails();

            if (!empty($verifiedEmails)) {
                if (in_array($newfromaddress, $verifiedEmails)) {
                    if (!in_array($newfromaddress, $getAllEmailIds)) {
                        $entity->setVerifiedEmails($newfromaddress);
                        $entity->setVerificationStatus('Verified');
                        $verifiedemailRepo->saveEntity($entity);

                        return;
                    }
                }
            }

            if ($isValidEmail == 'Policy not written') {
                $message = $this->translator->trans('le.email.verification.policy.error');
            }
            if (!in_array($newfromaddress, $getAllEmailIds) && $newfromaddress != '') {
                if (!$isValidEmail) {
                    $result = $this->emailValidator->sendVerificationMail($emailuser, $emailpassword, $region, $newfromaddress, $awscallbackurl);
                    if ($result == 'Policy not written') {
                        $message = $this->translator->trans('le.email.verification.policy.error');
                    } elseif ($result == 'Sns Policy not written') {
                        $message = $this->translator->trans('le.email.verification.sns.policy.error');
                    } else {
                        return;
                    }
                }
            } else {
                $result = $this->emailValidator->getEmailVerificationStatus($emailuser, $emailpassword, $region, $newfromaddress);
                if (!$result) {
                    $message = $this->translator->trans('le.email.verification.error');
                } elseif ($result) {
                    return;
                } elseif ($result == 'Policy not written') {
                    $message = $this->translator->trans('le.email.verification.policy.error');
                }
            }
            $this->context->addViolation($message);
        } else {
            return;
        }
    }
}
