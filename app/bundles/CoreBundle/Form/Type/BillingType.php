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
use Mautic\LeadBundle\Helper\FormFieldHelper;
use Mautic\ReportBundle\Model\ReportModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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

        $choices = FormFieldHelper::getRegionChoices();

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
                'required'    => true,
            ]
        );

        $builder->add(
            'companyaddress',
            'textarea',
            [
                'label'       => 'mautic.billing.companyaddress',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
            ]
        );

        $builder->add(
            'postalcode',
            'number',
            [
                'label'       => 'leadsengage.billing.postalcode',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
            ]
        );

        $builder->add(
            'city',
            'text',
            [
                'label'       => 'leadsengage.billing.city',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
            ]
        );

        $builder->add(
            'state',
            'choice',
            [
                'choices'     => $choices,
                'label'       => 'leadsengage.billing.state',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'empty_value' => '',
                'data'        => '',
                'data'        => $options['data']->getState(),
                'required'    => true,
            ]
        );

        $builder->add(
            'country',
            'choice',
            [
                'choices'     => FormFieldHelper::getCountryChoices(),
                'label'       => 'leadsengage.billing.country',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class'    => 'form-control',
                    'onchange' => 'Mautic.showGSTNumber(this.value);',
                ],
                'empty_value' => '',
                'data'        => $options['data']->getCountry(),
                'required'    => true,
            ]
        );

        $builder->add(
            'gstnumber',
            'text',
            [
                'label'       => 'leadsengage.billing.gstnumber',
                'label_attr'  => [
                    'class' => 'control-label',
                    'id'    => 'gstnumber_info',
                    'style' => 'display:none;',
                ],
                'attr'        => ['class' => 'form-control', 'style' => 'display:none;'],
                'required'    => false,
            ]
        );

        if ($options['isBilling']) {
            $builder->add(
                'accountingemail',
                'email',
                [
                    'label'      => 'mautic.billing.accountingemail',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => ['class' => 'form-control'],
                    'required'   => true,
                ]
            );
        }

        if ($options['isBilling']) {
            $builder->add(
                'buttons',
                'form_buttons'
            );
        }

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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'  => 'Mautic\CoreBundle\Entity\Billing',
                'isBilling'   => false,
            ]
        );

        $resolver->setRequired(['isBilling']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'billinginfo';
    }
}
