<?php

namespace RalphJSmit\Laravel\SEO\Schema;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class LocalBusinessSchema extends Schema
{
    public string|array $type;

    public string $name;
    public ?array $address;

    public function addAddress(?string $streetAddress = null, ?string $addressLocality = null, ?string $addressRegion = null, ?string $postalCode = null, ?string $addressCountry = null, string $type = 'Text'): static
    {
        $this->address = [
            "@type" => $type,
            "streetAddress" => $streetAddress,
            "addressLocality" => $addressLocality,
            "addressRegion" => $addressRegion,
            "postalCode" => $postalCode,
            "addressCountry" => $addressCountry
        ];

        return $this;
    }

    public function addName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function addType(string|array $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function initializeMarkup(SEOData $SEOData, array $markupBuilders): void
    {
        $defaults = [
            'name' => config('seo.site_name'),
            'type' => config('seo.local.@type'),
            'address' => config('seo.local.address', null),
        ];

        foreach ($defaults as $property => $defaultValue) {
            if ( !isset($this->{$property}) && !is_null($defaultValue) ) {
                $this->{$property} = $defaultValue;
            }
        }
    }

    public function generateInner(): HtmlString
    {
        $inner = collect([
            '@context' => 'https://schema.org',
            '@type' => $this->type,
            'name' => $this->name,
        ])
            ->when($this->address, fn (Collection $collection): Collection => $collection->put('address', $this->address))
            ->pipeThrough($this->markupTransformers)
            ->toJson();

        return new HtmlString($inner);
    }
}
