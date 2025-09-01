<?php

namespace Ihasan\FilamentMailerLite\Pipelines\Steps;

use Ihasan\FilamentMailerLite\Pipelines\SubscriberPipeline;

class SetCustomFields
{
    public function handle(SubscriberPipeline $pipeline, \Closure $next)
    {
        $data = $pipeline->getData();
        
        // Collect custom fields
        $fields = [];
        
        if (!empty($data['company'])) {
            $fields['company'] = $data['company'];
        }
        
        if (!empty($data['phone'])) {
            $fields['phone'] = $data['phone'];
        }
        
        // Add any other fields from the 'fields' array
        if (!empty($data['fields']) && is_array($data['fields'])) {
            $fields = array_merge($fields, $data['fields']);
        }
        
        if (!empty($fields)) {
            $pipeline->setBuilder(
                $pipeline->getBuilder()->withFields($fields)
            );
        }

        return $next($pipeline);
    }
}
