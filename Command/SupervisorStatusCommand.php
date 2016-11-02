<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorStatusCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('abc:supervisor:status');
        $this->setDescription('Shows status of supervisor instance');
        $this->setHelp(<<<'EOF'
The <info>%command.name%</info> shows status of supervisor instance:

    <info>php %command.full_name%</info>

The <info>--id</info> or <info>--host</info> parameter can be used to specify the supervisor instance
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
            $io->block($supervisor->getStatus());
        }
    }
}