<?php

namespace App\Form;

use App\Entity\State;
use App\Entity\FilterOrderAdmin;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminFilterOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state',EntityType::class,[
                'class' => State::class,
                'label' => 'Statut de la commande',
                'required' => false,
                'query_builder' => function (EntityRepository $stateRepository) {
                    return $stateRepository->createQueryBuilder('st')
                        ->orWhere('st.description=:name_cc')
                        ->orWhere('st.description=:name_cp')
                        ->orWhere('st.description=:name_cr')
                        ->setParameters([
                            ':name_cc' => 'Commande en cours',
                            ':name_cp' => 'Commande prête',
                            ':name_cr' => 'Commande récupérée'
                        ]);
                },
                'choice_label' => 'description',
                'choice_value' => 'id'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FilterOrderAdmin::class,
        ]);
    }
}
