<?php
/*
* This file is part of the supervisor-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\SupervisorBundle\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle as BaseStyle;

/**
 * @ToDo To be removed when dependency on Symfony 2.7 is bumped
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class SymfonyStyle extends BaseStyle
{
    /**
     * Formats a command comment.
     *
     * @param string|array $message
     */
    public function comment($message)
    {
        if(method_exists(BaseStyle::class, 'comment')) {
            parent::comment($message);
        }
        else {
            $message = str_replace('<info>', '', $message);
            $message = str_replace('</info>', '', $message);
            $this->block($message);
        }
    }
}