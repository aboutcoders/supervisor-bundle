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

use Abc\Bundle\SupervisorBundle\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SupervisorCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();

        $this->setName('abc:supervisor:status');
        $this->setDescription('Shows status of supervisor instance');
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

            $io->block(var_dump($supervisor->getStatus()));
        }
    }
}