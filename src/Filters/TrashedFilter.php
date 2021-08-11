<?php

namespace Flooris\Resource\Filters;

use Spatie\QueryBuilder\AllowedFilter;

class TrashedFilter extends AbstractFilter
{
    public function __construct(string $name = 'trashed', $internalName = null, ?string $label = null)
    {
        parent::__construct($name, $internalName, $label);

        $this->options([
            'with' => 'With',
            'only' => 'Only',
        ]);
    }

    public function allowedFilter(): AllowedFilter
    {
        return AllowedFilter::trashed($this->name, $this->internalName);
    }
}
