<?php

namespace RalphJSmit\Laravel\SEO;

use Closure;
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\Schema\FaqPageSchema;
use RalphJSmit\Laravel\SEO\Schema\LocalBusinessSchema;
use RalphJSmit\Laravel\SEO\Schema\Schema;

class SchemaCollection extends Collection
{
    protected array $dictionary = [
        'article' => ArticleSchema::class,
        'breadcrumbs' => BreadcrumbListSchema::class,
        'faqPage' => FaqPageSchema::class,
        'localBusiness' => LocalBusinessSchema::class,
    ];

    public array $markup = [];

    public function addArticle(?Closure $builder = null): static
    {
        $this->markup[$this->dictionary['article']][] = $builder ?: fn (Schema $schema): Schema => $schema;

        return $this;
    }

    public function addBreadcrumbs(?Closure $builder = null): static
    {
        $this->markup[$this->dictionary['breadcrumbs']][] = $builder ?: fn (Schema $schema): Schema => $schema;

        return $this;
    }


    public function addFaqPage(?Closure $builder = null): static
    {
        $this->markup[$this->dictionary['faqPage']][] = $builder ?: fn (Schema $schema): Schema => $schema;

        return $this;
     }

    public function addLocalBusiness(?Closure $builder = null): static
    {
        $this->markup[$this->dictionary['localBusiness']][] = $builder ?: fn (Schema $schema): Schema => $schema;

        return $this;
    }

    public static function initialize(): static
    {
        return new static();
    }
}
