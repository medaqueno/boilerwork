<?php

declare(strict_types=1);

namespace Kernel\Helpers;

enum Environments: string
{
case DEVELOPMENT = 'dev';
case TEST = 'test';
case PREPRODUCTION = 'pre';
case PRODUCTION = 'prod';
    }
