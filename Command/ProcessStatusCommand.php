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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ProcessStatusCommand extends BaseProcessCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:process:status');
        $this->setDescription('Shows status of supervisor processes');
        $this->setHelp(<<<'EOF'
The <info>%command.name%</info> shows status of supervisor processes:

    <info>php %command.full_name%</info>

The <info>--id</info> or <info>--host</info> parameter can be used to specify the supervisor instance

You can also optionally specify the name of a process group

    <info>php %command.full_name% --group=groupName</info>

You can also optionally specify the name of a process

    <info>php %command.full_name% --name=processName</info>
EOF
        );
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

        return $error ? 1 : 0;
    }

    /**
     * @param SymfonyStyle     $io
     * @param ProcessInterface $process
     */
    protected function status(SymfonyStyle $io, ProcessInterface $process)
    {
        if (in_array($process->getState(), [\Supervisor\Process::BACKOFF, \Supervisor\Process::EXITED, \Supervisor\Process::FATAL])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=white;bg=red', ' ', true);

        } elseif (in_array($process->getState(), [\Supervisor\Process::STARTING, \Supervisor\Process::STOPPING, \Supervisor\Process::UNKNOWN])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=black;bg=yellow', ' ', true);
        } elseif (in_array($process->getState(), [\Supervisor\Process::STOPPED])) {
            $io->block($process->getName(), $process->getStatename(), 'fg=white;bg=default', ' ', true);
        } else {
            $io->block($process->getName(), $process->getStatename(), 'fg=black;bg=green', ' ', true);
        }
    }
}