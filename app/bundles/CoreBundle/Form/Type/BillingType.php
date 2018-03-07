<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Form\Type;

use Mautic\CoreBundle\Form\EventListener\CleanFormSubscriber;
use Mautic\CoreBundle\Form\EventListener\FormExitSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\LanguageHelper;
use Mautic\ReportBundle\Model\ReportModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BillingType.
 */
class BillingType extends AbstractType
{
    /**
     * @var ReportModel
     */
    private $reportModel;

    /**
     * @var bool|mixed
     */
    private $supportedLanguages;

    /**
     * @var LanguageHelper
     */
    private $langHelper;

    /**
     * Translator object.
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;

    public function __construct(ReportModel $reportModel, TranslatorInterface $translator, LanguageHelper $languageHelper, CoreParametersHelper $parametersHelper)
    {
        $this->reportModel = $reportModel;
        $this->translator  = $translator;
        $this->langHelper  = $languageHelper;

        $languages   = $languageHelper->fetchLanguages(false, false);
        $langChoices = [];

        foreach ($languages as $code => $langData) {
            $langChoices[$code] = $langData['name'];
        }

        $langChoices = array_merge($langChoices, $parametersHelper->getParameter('supported_languages'));

        // Alpha sort the languages by name
        asort($langChoices);

        $this->supportedLanguages = $langChoices;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new CleanFormSubscriber());
        $builder->addEventSubscriber(new FormExitSubscriber('user.user', $options));

        $builder->add(
            'companyname',
            'text',
            [
                'label'      => 'mautic.billing.companyname',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'autocomplete' => 'off',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'companyaddress',
            'textarea',
            [
                'label'       => 'mautic.billing.companyaddress',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => false,
            ]
        );

        $builder->add(
            'accountingemail',
            'email',
            [
                'label'       => 'mautic.billing.accountingemail',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => false,
            ]
        );

        $builder->add(
            'buttons',
            'form_buttons'
        );

        // Get the list of available languages
        $languages   = $this->langHelper->fetchLanguages(false, false);
        $langChoices = [];

        foreach ($languages as $code => $langData) {
            $langChoices[$code] = $langData['name'];
        }

        $langChoices = array_merge($langChoices, $this->supportedLanguages);

        // Alpha sort the languages by name
        asort($langChoices);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'billinginfo';
    }
}
