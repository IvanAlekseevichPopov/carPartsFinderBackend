<?php

namespace App\Command;

use App\DBAL\Types\Enum\UserRoleTypeEnum;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Create user',
)]
class UserCreateCommand extends Command
{
    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        string $name = null,
    )
    {
        parent::__construct($name);
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('pass', InputArgument::REQUIRED, 'User password')
            ->addOption('role', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'User roles', ['ROLE_USER'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $pass = $input->getArgument('pass');

        if(empty($email) || empty($pass)) {
            $io->error('Email or password is empty');
            return Command::FAILURE;
        }
        if($this->userRepository->findOneBy(['email' => $email])) {
            $io->error('User already exists');
            return Command::FAILURE;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->hasher->hashPassword($user, $pass));
        foreach ($input->getOption('role') as $role) {
            $user->addRole(UserRoleTypeEnum::fromName($role));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $io->success('User created');

        $user->setPassword($pass);

        return Command::SUCCESS;
    }
}
