<?php

namespace Flooris\Resource\Fields;

use Flooris\LaravelMoney\Money;

class Currency extends AbstractField
{
    public string $component = 'CurrencyField';


    public function __construct(string $name, null|string|callable $attribute = null, ?string $label = null)
    {
        parent::__construct($name, $attribute, $label);
        $this->resolveCallback(fn (?Money $value) => $value ? $value->formatByIntl() : $value);
    }
}
