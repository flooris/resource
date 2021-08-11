<?php

namespace Flooris\Resource\Fields;

use DateTimeInterface;

class Date extends DateTime
{
    public string $component = 'DateField';
    protected string $format = "d-m-Y";
}