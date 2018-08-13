<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PromoteUserCommand extends AbstractUserCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('app:user:promote')
            ->setDescription('Promotes a user by adding a role')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::REQUIRED, 'Set the users role'),
            ));

        parent::configure();
    }
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $email   = $input->getArgument('email');
        $role      = $input->getArgument('role');

        $this->userUtil->promote($email, $role);

        $output->writeln(sprintf('Role "%s" has been added to user "%s".', $role, $email));
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

        if (!$input->getArgument('role')) {
            $question = new Question('Please choose an role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('Role can not be empty');
                }
                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function getHelpText(): string
    {
        return <<<EOT
The <info>app:user:promote</info> command promotes a user by adding a role
  <info>php app/console app:user:promote matthieu@email.com ROLE_CUSTOM</info>
EOT;
    }
}
