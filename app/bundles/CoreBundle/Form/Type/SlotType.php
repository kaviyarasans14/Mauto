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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SlotType.
 */
class SlotType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'width',
            'number',
            [
                'label'      => 'Width',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'width',
                    'postaddon_text'  => 'px',
                ],
            ]
        );

        $builder->add(
            'padding-top',
            'number',
            [
                'label'      => 'mautic.core.padding.top',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'padding-top',
                    'postaddon_text'  => 'px',
                ],
            ]
        );

        $builder->add(
            'padding-bottom',
            'number',
            [
                'label'      => 'mautic.core.padding.bottom',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'padding-bottom',
                    'postaddon_text'  => 'px',
                ],
            ]
        );

        $builder->add(
            'padding-left',
            'number',
            [
                'label'      => 'mautic.core.padding.left',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'padding-left',
                    'postaddon_text'  => 'px',
                ],
            ]
        );

        $builder->add(
            'padding-right',
            'number',
            [
                'label'      => 'mautic.core.padding.right',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'           => 'form-control',
                    'data-slot-param' => 'padding-right',
                    'postaddon_text'  => 'px',
                ],
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'slot';
    }
}
