<?php
namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddAdminUserCommand extends Command
{
protected static $defaultName = 'app:add-admin';

private $entityManager;
private $passwordEncoder;

public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
{
parent::__construct();

$this->entityManager = $entityManager;
$this->passwordEncoder = $passwordEncoder;
}

protected function configure()
{
$this->setDescription('Ajoute un utilisateur administrateur.');
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
$output->writeln('Création d\'un utilisateur admin...');

$user = new User();
$user->setEmail('gregory.girault88@gmail.com');
$user->setRoles(['ROLE_ADMIN']);
$user->setNom('AdminNom');
$user->setPrenom('AdminPrenom');

$password = $this->passwordEncoder->encodePassword($user, 'password');
$user->setPassword($password);

$this->entityManager->persist($user);
$this->entityManager->flush();

$output->writeln('L\'utilisateur admin a été créé avec succès!');

return Command::SUCCESS;
}
}