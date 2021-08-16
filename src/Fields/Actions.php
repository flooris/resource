<?php

namespace Flooris\Resource\Fields;

use Illuminate\Support\Collection;
use Flooris\Resource\Fields\Link\FieldUrl;
use Flooris\Resource\Fields\Link\FieldLink;
use Flooris\Resource\Fields\Link\FieldRoute;
use Flooris\Resource\Fields\Link\FieldAction;

class Actions extends AbstractField
{
    public string $component = 'ActionsField';
    protected Collection $links;

    public function __construct(string $name = 'actions')
    {
        $this->links = Collection::make();

        parent::__construct($name, function (mixed $resource) {
            return $this->links->each(function (FieldLink $link) use ($resource) {
                $link->resolve($resource);
            })
                ->filter(fn (FieldLink $link) => $link->getHref())
                ->values();
        });

        $this->label(__('entities.general.fields.actions.label'));
    }

    public function link(string|callable $href, string $name, string $method = 'GET'): static
    {
        $this->links->push(FieldUrl::make($href, $name, $method));

        return $this;
    }

    public function route(string $name, array $parameters = [], ?callable $callback = null): static
    {
        $this->links->push(FieldRoute::make($name, $parameters, $callback));

        return $this;
    }

    public function action(string|array $name, array $parameters = [], ?callable $callback = null): static
    {
        $this->links->push(FieldAction::make($name, $parameters, $callback));

        return $this;
    }
}
