<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'user:create-user',
    description: 'Create a user',
)]
class CreateUserCommand extends Command
{
    private const NAME = 'user:create-user';

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'username')
            ->addArgument('email', InputArgument::REQUIRED, 'email')
            ->addArgument('password', InputArgument::REQUIRED, 'password')
            ->addArgument('role', InputArgument::REQUIRED, 'role');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $style = new SymfonyStyle($input, $output);

        $questions = array();
        $questions['username'] = $style->ask("username(firstname and lastname)");
        $questions['email'] = $style->ask("email");
        if (!$input->getArgument('password')) {
            $question = $style->askHidden('Please choose a password: ', function ($password) {
                if (empty($password)) {
                    throw new Exception('Password can not be empty');
                }

                return $password;
            });
            $questions['password'] = $question;
        }
        $questions['role'] = $style->ask('set the user role: (admin/user/superAdmin) ');

        foreach ($questions as $name => $question) {
            $input->setArgument($name, $question);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $style = new SymfonyStyle($input, $output);
        $style->title('Create a user');

        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        switch ($role) {
            case 'admin':
                $user->setRoles(['ROLE_ADMIN']);
                break;
            case 'superAdmin':
                $user->setRoles(['ROLE_SUPER_ADMIN']);
                break;
            default:
                $user->setRoles(['ROLE_USER']);
        }
        $this->userRepository->add($user, true);

        $style->success('The user has been successfully created!');

        return Command::SUCCESS;
    }
}
