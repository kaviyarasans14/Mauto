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
            $result = $this->emailValidator->getEmailVerificationStatus($emailuser, $emailpassword, $region, $newfromaddress);
            if (!$result) {
                $message = $this->translator->trans('le.email.verification.error');
            } elseif (strpos('Policy not written', $result)) {
                $message = $this->translator->trans('le.email.verification.policy.error');
            } elseif ($result) {
                return;
            }
            $this->context->addViolation($message);
        } else {
            return;
        }
    }
}
