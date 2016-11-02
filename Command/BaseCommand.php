<?php
/*
* This file is part of the supervisor-command-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Command;

use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Abc\Bundle\SupervisorBundle\Supervisor\SupervisorManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    /**
     * @var Supervisor[]
     */
    protected $supervisors = array();

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'The supervisor id');
        $this->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'The supervisor host');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('id') && $input->getOption('host')) {
            $io->error('You can either specify the option "id" or the option "host"');

            return;
        }

        $supervisors = $this->findSupervisors($input);
        if (count($supervisors) > 0) {
            $this->supervisors = $supervisors;
        } elseif (!$input->getOption('id') && !$input->getOption('host')) {
            $io->error('No supervisor instance configured');
        } else {
            $option = $input->getOption('id') ? 'id' : 'host';
            $value  = $input->getOption('id') ? $input->getOption('id') : $input->getOption('host');
            $io->error(sprintf('No supervisor instance configured for %s "%s"', $option, $value));
        }
    }

    /**
     * Returns the supervisors
     *
     * @param InputInterface $input
     * @return Supervisor[]
     */
    protected function findSupervisors(InputInterface $input)
    {
        if ($id = $input->getOption('id')) {
            $supervisor = $this->getManager()->findById($id);

            return null === $supervisor ? [] : [$supervisor];
        } elseif ($host = $input->getOption('host')) {
            $supervisor = $this->getManager()->findByHost($host);

            return null === $supervisor ? [] : [$supervisor];
        } else {
            return $this->getManager()->findAll();
        }
    }

    /**
     * @return SupervisorManager
     */
    protected function getManager()
    {
        return $this->getContainer()->get('abc.supervisor.manager');
    }
}