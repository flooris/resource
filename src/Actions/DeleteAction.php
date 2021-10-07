<?php

namespace Flooris\Resource\Actions;

use Illuminate\Database\Eloquent\Model;

class DeleteAction extends AbstractAction
{
    protected string $component = 'ConfirmMultiDeleteModal';

    public function resolve(mixed $resource): void
    {
        if ($resource instanceof Model) {
            $resourceClassName      = $resource::class;
            $resourceTranslationKey = "entities.$resourceClassName.label";

            $name = trans_choice($resourceTranslationKey, 2);
        }

        $modalClassName      = static::class;
        $modalTranslationKey = "modals.$modalClassName";

        $this->title       = __("$modalTranslationKey.title", [
            'name' => $name ?? 'Resources',
        ]);
        $this->description = __("$modalTranslationKey.description", [
            'name' => $name ?? 'Resources',
        ]);
    }
}
