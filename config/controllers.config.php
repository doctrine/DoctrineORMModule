<?php

namespace DoctrineORMModule;

use DoctrineORMModule\Yuml\YumlController;
use DoctrineORMModule\Yuml\YumlControllerFactory;

return [
    'factories' => [
        // Yuml controller, used to generate Yuml graphs since
        // yuml.me doesn't do redirects on its own
        YumlController::class => YumlControllerFactory::class,
    ],
];
