<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu')
            ->add('imageForm', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Ajouter une image'
            ])

            //Ci-après on ajoute le champ auteur, 'auteur' attend un objet car dans entity l30 on lui dit que c'est un objet et non un string
            ->add('auteur', EntityType::class, [
                'placeholder' => 'Choisissez un auteur',//un placeholder classique
                'class' => Auteur::class,//objet de la classe auteur 
                'choice_label' => 'fullName',//on selectionne ce qu'on souhaite afficher sur la page  ici le nom complet grace à notre méthode crée dans entity Auteur
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Choisissez une ou plusieurs catégories:',
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'required' => false,
                'multiple' => true, //It's because you're in a ManyToMany or OneToMany relationship. You have to tell to the formType it's multiple, if not it will try to use the collection as an Entity. That's why it's telling you that it cannot persist a PersistentCollection Entity because it's not managed
                'expanded' => true,
            ])
            ->add('envoyer', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
