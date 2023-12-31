<?php

namespace App\Controller\Admin;

# Importation de l'entité gérée par le CRUD
use App\Entity\Article;

# Pour gérer le CRUD
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

# Utilisation des champs de EasyAdmin
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class ArticleCrudController extends AbstractCrudController
{
    # Définition de l'entité gérée par le CRUD
    # https://symfony.com/bundles/EasyAdminBundle/current/crud.html#page-names-and-constants
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    # Options de configuration du CRUD
    # https://symfony.com/bundles/EasyAdminBundle/current/crud.html#crud-controller-configuration
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // classés par id décroissant
            ->setDefaultSort(['id' => 'DESC'])
            // Nombre d'articles par page
            ->setPaginatorPageSize(20)
            // Titres des pages
            ->setPageTitle('index', 'Liste des articles')
            ->setPageTitle('new', 'Créer un article')
            ->setPageTitle('edit', 'Modifier un article');

    }

    # Champs à afficher dans le CRUD
    # https://symfony.com/bundles/EasyAdminBundle/current/fields.html#field-types
    public function configureFields(string $pageName): iterable
    {
        return [
            # id seulement sur l'accueil
            IntegerField::new('id')->onlyOnIndex(),
            TextField::new('ArticleTitle')->setFormTypeOptions([
                'label' => 'Titre',]),
            # slug seulement sur les formulaires,
            # lié au titre avec création automatique
            SlugField::new('ArticleSlug')->onlyOnForms()
                ->setTargetFieldName('ArticleTitle')
                ->setFormTypeOptions([
                    'label' => 'Slug',
                    'help' => 'Créé à partir du titre, modifiable en cliquant sur le cadenas',]),
            TextEditorField::new('ArticleContent'),
            DateTimeField::new('ArticleDateCreate'),
            # date de l'update cachée sur l'accueil
            DateTimeField::new('ArticleDateUpdate')->hideOnIndex(),
            BooleanField::new('ArticleIsPublished'),
            # Panel pour regrouper les champs
            FormField::addPanel('Lien avec les autres tables'),
            #
            # Association avec les autres tables
            #
            # https://symfony.com/bundles/EasyAdminBundle/current/fields/AssociationField.html
            # Lien avec la table utilisateur ManyToOne
            AssociationField::new('utilisateur'),
            # Lien avec la table commentaire OneToMany
            AssociationField::new('Commentaires')->setFormTypeOptions([
                'by_reference' => false,
                'label' => 'Commentaires',
                'help' => 'Ajouter, modifier ou supprimer un commentaire',
            ]),
            # Lien avec la table catégorie ManyToMany -
            # Il faut ajouter le setFormTypeOptions pour éviter que les catégories
            # ne soient pas ajoutées, modifiées ou supprimées !
            # https://stackoverflow.com/questions/65900855/easyadmin-manytomany-relation-not-saving-data-in-base
            AssociationField::new('categories')->setFormTypeOptions([
                'by_reference' => false,
            ]),
        ];
    }
}

