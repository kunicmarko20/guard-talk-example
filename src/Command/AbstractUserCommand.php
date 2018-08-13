<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use App\Service\UserCommandUtil;

abstract class AbstractUserCommand extends ContainerAwareCommand
{
    protected $userUtil;

    public function __construct(UserCommandUtil $userUtil)
    {
        $this->userUtil = $userUtil;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp($this->getHelpText());
    }

    abstract protected function getHelpText(): string;
}
