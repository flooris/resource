<?php

namespace Flooris\Resource\Modals;

use JsonSerializable;
use Flooris\Resource\Traits\Make;
use Flooris\Resource\Interfaces\Makeable;
use Flooris\Resource\Fields\AbstractField;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;

class ConfirmDelete extends AbstractModal
{
    protected string $component = 'ConfirmDeleteModal';

    public function resolve(mixed $resource): void
    {
        if ($resource instanceof Model) {
            $resourceClassName      = $resource::class;
            $resourceTranslationKey = "entities.$resourceClassName.label";

            $name = trans_choice($resourceTranslationKey, 1);
            $id   = $resource->getKey();
        }

        $modalClassName      = static::class;
        $modalTranslationKey = "modals.$modalClassName";

        $this->title       = __("$modalTranslationKey.title", [
            'name' => $name ?? 'Resource',
            'id'   => $id ?? null,
        ]);
        $this->description = __("$modalTranslationKey.description", [
            'name' => $name ?? 'Resource',
        ]);
    }
}