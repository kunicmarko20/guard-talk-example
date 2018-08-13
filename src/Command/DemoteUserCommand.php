<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DemoteUserCommand extends AbstractUserCommand
{
    /**
     * @see Command
     */
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('app:user:demote')
            ->setDescription('Demotes a user by removing a role')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('role', InputArgument::REQUIRED, 'Removes the users role'),
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

        $this->userUtil->demote($email, $role);

         $output->writeln(sprintf('Role "%s" has been removed from user "%s".', $role, $email));
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
The <info>app:user:demote</info> command demotes a user by removing a role
  <info>php app/console app:user:demote matthieu@email.com ROLE_CUSTOM</info>
EOT;
    }
}
