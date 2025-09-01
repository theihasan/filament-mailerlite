<?php

namespace Ihasan\FilamentMailerLite\Pipelines;

use Ihasan\LaravelMailerlite\Facades\MailerLite;
use Ihasan\FilamentMailerLite\Pipelines\Steps\SetEmail;
use Ihasan\FilamentMailerLite\Pipelines\Steps\SetName;
use Ihasan\FilamentMailerLite\Pipelines\Steps\SetCustomFields;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\App;

class SubscriberPipeline
{
    protected array $data;
    protected $builder;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->builder = MailerLite::subscribers();
    }

    public static function create(array $data): static
    {
        return new static($data);
    }

    public function process(): array
    {
        return App::make(Pipeline::class)
            ->send($this)
            ->through([
                SetEmail::class,
                SetName::class,
                SetCustomFields::class,
            ])
            ->then(fn($pipeline) => $pipeline->builder->subscribe());
    }

    public function update(string $mailerliteId): array
    {
        return App::make(Pipeline::class)
            ->send($this)
            ->through([
                SetEmail::class,
                SetName::class,
                SetCustomFields::class,
            ])
            ->then(fn($pipeline) => $pipeline->builder->update($mailerliteId));
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getBuilder()
    {
        return $this->builder;
    }

    public function setBuilder($builder): self
    {
        $this->builder = $builder;
        return $this;
    }
}
