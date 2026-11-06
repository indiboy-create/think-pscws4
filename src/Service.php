<?php

namespace Youvision\ThinkPscws4;

use think\Service as ThinkService;

class Service extends ThinkService
{
    public function register(): void
    {
        $defaults = require __DIR__ . '/../config/pscws4.php';
        $config = (array) $this->app->config->get('pscws4', []);
        $this->app->config->set(array_merge($defaults, $config), 'pscws4');

        $this->app->bind('pscws4', function ($app) {
            return new Pscws4Manager(
                (array) $app->config->get('pscws4', []),
                $app->getRootPath()
            );
        });

        $this->app->bind(Pscws4Manager::class, function ($app) {
            return $app->make('pscws4');
        });
    }
}