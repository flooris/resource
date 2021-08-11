<?php


namespace Flooris\Resource\Fields\Link;


interface FieldLink
{
    public function resolve(mixed $resource): void;
    public function getHref(): string;
    public function getMethod(): string;
    public function getName(): string;
}