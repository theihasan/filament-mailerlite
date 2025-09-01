<?php

namespace Ihasan\FilamentMailerLite\Pipelines\Steps;

use Ihasan\FilamentMailerLite\Pipelines\SubscriberPipeline;

class SetEmail
{
    public function handle(SubscriberPipeline $pipeline, \Closure $next)
    {
        $data = $pipeline->getData();
        
        if (!empty($data['email'])) {
            $pipeline->setBuilder(
                $pipeline->getBuilder()->email($data['email'])
            );
        }

        return $next($pipeline);
    }
}
