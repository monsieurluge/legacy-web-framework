<?php

namespace monsieurluge\lwfdemo\Config;

use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Services;
use monsieurluge\lwfdemo\App\Service\Dieded;

final class DummyServices implements Services
{
    /**
     * @inheritDoc
     */
    public function declareTo(ServiceProvider $provider): void
    {
        $services = $this->services();

        foreach ($services as $name => $factory) {
            $provider->register($name, $factory);
        }
    }

    private function services(): array
    {
        return [
            'dieded' => function () { return new Dieded(); }
        ];
    }
}
