<?php

namespace Flooris\Resource\Fields;

use DateTimeInterface;

class Date extends DateTime
{
    protected string $component = 'DateField';
    protected string $format = "d-m-Y";
}
