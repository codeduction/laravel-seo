<?php

namespace RalphJSmit\Laravel\SEO\Schema;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ArticleSchema extends Schema
{
    public array $authors = [];

    public ?CarbonInterface $datePublished = null;

    public ?CarbonInterface $dateModified = null;

    public ?string $description = null;

    public ?string $headline = null;

    public ?string $image = null;

    public string $type = 'Article';

    public ?string $url = null;

    public ?string $articleBody = null;

    public function addAuthor(string $authorName): static
    {
        if (empty($this->authors)) {
            $this->authors = [
                '@type' => 'Person',
                'name' => $authorName,
            ];

            return $this;
        }

        $this->authors = [
            $this->authors,
            [
                '@type' => 'Person',
                'name' => $authorName,
            ],
        ];

        return $this;
    }

    public function initializeMarkup(SEOData $SEOData, array $markupBuilders): void
    {
        $this->url = $SEOData->url;

        $properties = [
            'headline' => 'title',
            'description' => 'description',
            'image' => 'image',
            'datePublished' => 'published_time',
            'dateModified' => 'modified_time',
            'articleBody' => 'articleBody',
        ];

        foreach ($properties as $markupProperty => $SEODataProperty) {
            if ($SEOData->{$SEODataProperty}) {
                $this->{$markupProperty} = $SEOData->{$SEODataProperty};
            }
        }

        if ($SEOData->author) {
            $this->authors = [
                '@type' => 'Person',
                'name' => $SEOData->author,
            ];
        }
    }

    public function generateInner(): HtmlString
    {
        $inner = collect([
            '@context' => 'https://schema.org',
            '@type' => $this->type,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->url,
            ],
        ])
            ->when($this->datePublished, fn (Collection $collection): Collection => $collection->put('datePublished', $this->datePublished->toIso8601String()))
            ->when($this->dateModified, fn (Collection $collection): Collection => $collection->put('dateModified', $this->dateModified->toIso8601String()))
            ->put('headline', $this->headline)
            ->when($this->authors, fn (Collection $collection): Collection => $collection->put('author', $this->authors))
            ->when($this->description, fn (Collection $collection): Collection => $collection->put('description', $this->description))
            ->when($this->image, fn (Collection $collection): Collection => $collection->put('image', $this->image))
            ->when($this->articleBody, fn (Collection $collection): Collection => $collection->put('articleBody', $this->articleBody))
            ->pipeThrough($this->markupTransformers)
            ->toJson();

        return new HtmlString($inner);
    }
}
