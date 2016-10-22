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
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorStopCommand extends SupervisorCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this
            ->setName('abc:supervisor:stop')
            ->setDescription('Stops the supervisor processes');
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
            if ($process = $input->getOption('process')) {
                try {
                    $error = $this->stopProcess($io, $supervisor, $supervisor->getProcess($process));
                } catch (\InvalidArgumentException $e) {
                    $io->error($e->getMessage());
                    $error = true;
                }
            } else {
                $processes = $supervisor->getProcesses($input->getOption('group'));
                foreach ($processes as $process) {
                    $error = $this->stopProcess($io, $supervisor, $process);
                }

                if (($group = $input->getOption('group')) && count($processes) == 0) {
                    $io->error(sprintf('A process with group "%s" does not exist', $group));
                }
            }
        }

        return $error ? 1 : 0;
    }

    /**
     * @param SymfonyStyle $io
     * @param Supervisor   $supervisor
     * @param Process      $process
     * @return bool Whether an error occurred
     */
    protected function stopProcess(SymfonyStyle $io, Supervisor $supervisor, Process $process)
    {
        $error = false;
        $io->comment(sprintf('Stopping process <info>%s</info>', $process->getName()));
        try {
            $supervisor->stop($process->getName());
            $io->success('Stopped');
        } catch (\Exception $e) {
            if (false !== strpos($e->getMessage(), 'NOT_RUNNING')) {
                $io->warning('Not running');
            } else {
                $error = true;
                $io->error($e->getMessage());
            }
        }

        return $error;
    }
}