<?php

namespace App\Controller\Admin;

use App\Entity\Taches;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;


class TacheCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Taches::class;
    }

    public function configureCrud(Crud $crud): Crud 
    {
        return $crud
            ->setEntityLabelInPlural('Taches')
            ->setEntityLabelInSingular('Tache')
            ->setPaginatorPageSize(10)
            ->setPageTitle('index', 'to_do_list - Administration des taches');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('titre');
        yield TextField::new('description');
        yield TextField::new('statut');
        yield TextField::new('priorite');
        yield DateTimeField::new('date');
    }

}
