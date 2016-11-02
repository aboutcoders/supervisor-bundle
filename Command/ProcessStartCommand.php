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

use Abc\Bundle\SupervisorBundle\Console\Style\SymfonyStyle;
use Abc\Bundle\SupervisorBundle\Supervisor\ProcessInterface;
use Abc\Bundle\SupervisorBundle\Supervisor\Supervisor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessStartCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:process:start');
        $this->setDescription('Starts supervisor processes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $error = false;
        $io    = new SymfonyStyle($input, $output);
        foreach ($this->supervisors as $supervisor) {
            $io->section(sprintf('%s (%s)', $supervisor->getId(), $supervisor->getHost()));
            if ($process = $input->getOption('process')) {
                try {
                    $error = $this->startProcess($io, $supervisor, $supervisor->getProcess($process));
                } catch (\InvalidArgumentException $e) {
                    $io->error($e->getMessage());
                    $error = true;
                }
            } else {
                $processes = $supervisor->getProcesses($input->getOption('group'));
                foreach ($processes as $process) {
                    $error = $this->startProcess($io, $supervisor, $process);
                }

                if (($group = $input->getOption('group')) && count($processes) == 0) {
                    $io->error(sprintf('A process with group "%s" does not exist', $group));
                }
            }
        }

        return $error ? 1 : 0;
    }

    /**
     * @param SymfonyStyle     $io
     * @param Supervisor       $supervisor
     * @param ProcessInterface $process
     * @return bool Whether an error occurred
     */
    protected function startProcess(SymfonyStyle $io, Supervisor $supervisor, ProcessInterface $process)
    {
        $error = false;
        $io->comment(sprintf('Starting process <info>%s</info>', $process->getName()));
        try {
            $supervisor->startProcess($process);
            $io->success('Started');
        } catch (\Exception $e) {
            if (false !== strpos($e->getMessage(), 'ALREADY_STARTED')) {
                $io->warning('Already started');
            } else {
                $error = true;
                $io->error($e->getMessage());
            }
        }

        return $error;
    }
}