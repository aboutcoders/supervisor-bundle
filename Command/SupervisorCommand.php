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

/**i
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class SupervisorCommand extends ContainerAwareCommand
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
        $this->addOption('key', 'k', InputOption::VALUE_OPTIONAL, 'The supervisor key');
        $this->addOption('host', 'H', InputOption::VALUE_OPTIONAL, 'The supervisor host');
        $this->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'The name of the process group');
        $this->addOption('process', 'p', InputOption::VALUE_OPTIONAL, 'The name of the process');
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('key') && $input->getOption('host')) {
            $io->error('You can either specify the option "key" or the option "host"');
        }

        $supervisors = $this->findSupervisors($input);
        if (count($supervisors) > 0) {
            $this->supervisors = $supervisors;
        } else {
            $key   = $input->getOption('key') ? 'key' : 'host';
            $value = $input->getOption('key') ? $input->getOption('key') : $input->getOption('host');
            $io->error(sprintf('No supervisor instance configured for %s "%s"', $key, $value));
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
        if ($key = $input->getOption('key')) {
            $supervisor = $this->getManager()->findByKey($key);

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