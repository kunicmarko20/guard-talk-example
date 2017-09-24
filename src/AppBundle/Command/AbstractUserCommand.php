<?php
/**
 * Created by PhpStorm.
 * User: Marko Kunic
 * Date: 9/13/17
 * Time: 11:20
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Service\UserCommandUtil;

abstract class AbstractUserCommand extends ContainerAwareCommand
{
    protected $userUtil;

    public function __construct(UserCommandUtil $userUtil)
    {
        $this->userUtil = $userUtil;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp($this->getHelpText());
    }

    abstract protected function getHelpText();
}
