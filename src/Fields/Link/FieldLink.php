<?php

namespace Flooris\Resource\Fields\Link;

use Flooris\Resource\Modals\Modal;

interface FieldLink
{
    public function resolve(mixed $resource): void;

    public function modal(Modal $modal): static;

    public function getHref(): ?string;

    public function getMethod(): string;

    public function getName(): string;
}