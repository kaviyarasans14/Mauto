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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AccountType.
 */
class AccountType extends AbstractType
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

        $currencyChoices = [
            'rupee'            => 'Rupee',
            'usdollar'         => 'United States dollar',
            'euro'             => 'Euro',
            'pound'            => 'Pound Sterling',
            'autraliandollar'  => 'Australian dollar',
            'newdollar'        => 'New Zealand dollar',
            'norwegiankrone'   => 'Norwegian krone',
            'swedishkrona'     => 'Swedish krona',
            'swissfrancs'      => 'Swiss Francs',
            'canadiandollar'   => 'Canadian dollar',
        ];

        $builder->add(
            'accountname',
            'text',
            [
                'label'      => 'mautic.core.accountname',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control',
                    'autocomplete' => 'off',
                ],
                'required'    => true,
            ]
        );

        $builder->add(
            'domainname',
            'text',
            [
                'label'       => 'mautic.core.domainname',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control', 'style' => 'pointer-events: none;background-color: #ebedf0;opacity: 1;'],
                'required'    => true,
            ]
        );

        $builder->add(
            'email',
            'email',
            [
                'label'       => 'mautic.core.accountemail',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
            ]
        );

        $builder->add(
            'phonenumber',
            'text',
            [
                'label'       => 'mautic.core.accountmobile',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
            ]
        );

        $builder->add(
            'currencysymbol',
            'text',
            [
                //'choices'    => $currencyChoices,
                'label'       => 'mautic.core.accountcurrency',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control', 'style' => 'pointer-events: none;background-color: #ebedf0;opacity: 1;'],
                'required'    => false,
            ]
        );
        $timezones = FormFieldHelper::getCustomTimezones();
        $builder->add(
            'timezone',
            'choice',
            [
                'choices'    => $timezones,
                'label'      => 'mautic.core.accounttimezone',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control accountTimezone',
                    'tooltip' => 'mautic.core.config.form.default.timezone.tooltip',
                ],
                'multiple'    => false,
                'empty_value' => '',
                'data'        => $options['data']->getTimezone(),
                'required'    => true,
            ]
        );

        $builder->add(
            'accountid',
            'text',
            [
                'label'       => 'mautic.core.accountid',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control', 'style' => 'pointer-events: none;background-color: #ebedf0;opacity: 1;'],
                'required'    => false,
            ]
        );

        $builder->add(
            'needpoweredby',
            'yesno_button_group',
            [
                'label'      => 'mautic.core.accountpowered',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'disabled'   => 1,
                'data'       => true,
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
        return 'accountinfo';
    }
}
