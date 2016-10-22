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

use Abc\Bundle\SupervisorBundle\Supervisor\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorStatusCommand extends SupervisorCommand
{

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:status');
        $this->setAliases(['abc:supervisor:list']);
        $this->setDescription('Shows status of supervisor processes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = false;
        $io    = new SymfonyStyle($input, $output);
        foreach ($this->supervisors as $supervisor) {
            $io->section(sprintf('%s (%s)', $supervisor->getName(), $supervisor->getHost()));
            var_dump($supervisor->getClient()->getState());
            return;

            if ($process = $input->getOption('process')) {
                try {
                    $this->status($io, $supervisor->getProcess($process));
                } catch (\InvalidArgumentException $e) {
                    $io->error($e->getMessage());
                    $error = true;
                }
            } else {
                $processes = $supervisor->getProcesses($input->getOption('group'));
                foreach ($processes as $process) {
                    $this->status($io, $process);
                }

                if (($group = $input->getOption('group')) && count($processes) == 0) {
                    $io->error(sprintf('A process with group "%s" does not exist', $group));
                }
            }
        }

        return $error;
    }

    /**
     * @param SymfonyStyle $io
     * @param Process      $process
     */
    protected function status(SymfonyStyle $io, Process $process)
    {
        if (in_array($process->getState(), [\Supervisor\Process::BACKOFF, \Supervisor\Process::EXITED, \Supervisor\Process::FATAL])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=white;bg=red', ' ', true);

        } elseif (in_array($process->getState(), [\Supervisor\Process::STARTING, \Supervisor\Process::STOPPING, \Supervisor\Process::UNKNOWN])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=black;bg=yellow', ' ', true);
        }
        elseif(in_array($process->getState(), [\Supervisor\Process::STOPPED])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=white;bg=gray', ' ', true);
        }
        else {
            $io->block($process->getName(), $process->getStatename(), 'fg=black;bg=green', ' ', true);
        }
    }
}