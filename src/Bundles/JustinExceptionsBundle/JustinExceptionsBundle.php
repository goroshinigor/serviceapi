<?php

namespace App\Bundles\JustinExceptionsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use App\Bundles\JustinExceptionsBundle\DependencyInjection\JustinExceptionsExtension;

/*
 * 
 */
class JustinExceptionsBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new JustinExceptionsExtension();
    }
}