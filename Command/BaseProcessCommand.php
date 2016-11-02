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

use Symfony\Component\Console\Input\InputOption;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
abstract class BaseProcessCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        parent::configure();
        $this->addOption('group', 'g', InputOption::VALUE_OPTIONAL, 'The name of the process group');
        $this->addOption('process', 'p', InputOption::VALUE_OPTIONAL, 'The name of the process');
    }
}