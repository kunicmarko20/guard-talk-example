<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends AbstractUserCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        $this
            ->setName('app:user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputArgument('role', null, 'Set the users role'),
            ]);

        parent::configure();
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');

        $this->userUtil->create($email, $password, $role);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }
                return $email;
            });
            $questions['email'] = $question;
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }
                if (strlen($password) < 6) {
                    throw new \Exception('Password has to be at least 6 characters long');
                }
                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function getHelpText(): string
    {
        return <<<EOT
The <info>app:user:create</info> command creates a user:
  <info>php app/console app:user:create matthieu@example.com</info>
This interactive shell will ask you for an password.
You can alternatively specify the email and password as the first and second arguments:
  <info>php bin/console app:user:create matthieu@example.com mypassword</info>
You can create a super admin via the super-admin flag:
  <info>php app/console app:user:create matthieu@example.com mypassword ROLE_SUPER_ADMIN</info>
EOT;
    }
}
