<?php

namespace Ihasan\FilamentMailerLite\Pipelines\Steps;

use Ihasan\FilamentMailerLite\Pipelines\SubscriberPipeline;

class SetName
{
    public function handle(SubscriberPipeline $pipeline, \Closure $next)
    {
        $data = $pipeline->getData();
        
        if (!empty($data['name'])) {
            $pipeline->setBuilder(
                $pipeline->getBuilder()->named($data['name'])
            );
        }

        return $next($pipeline);
    }
}
