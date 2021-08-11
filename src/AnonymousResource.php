<?php


namespace Flooris\Resource;


use Illuminate\Http\Request;

class AnonymousResource extends Resource
{
    public function __construct(mixed $subject, ?Request $request = null, private array $anonymousFields = [])
    {
        parent::__construct($subject, $request);
    }

    public function fields(): array
    {
        return $this->anonymousFields;
    }
}