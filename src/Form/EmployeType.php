<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email', EmailType::class)
            ->add('statut')
            ->add('dateArrivee', DateType::class, ['widget' => 'single_text', 'label' => 'Date d\'entrée'])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    'Collaborateur' => 'ROLE_USER',
                    'Chef de projet' => 'ROLE_ADMIN',
                ],
            ])
        ;

        // Transformer pour convertir entre string (form) et array (entity)
        $builder->get('roles')
            ->addModelTransformer(new \Symfony\Component\Form\CallbackTransformer(
                function ($rolesAsArray) {
                    // array (entity) -> string (form)
                    return $rolesAsArray[0] ?? null;
                },
                function ($roleAsString) {
                    // string (form) -> array (entity)
                    return $roleAsString ? [$roleAsString] : [];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
