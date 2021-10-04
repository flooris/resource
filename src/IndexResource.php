<?php

namespace Flooris\Resource;

use stdClass;
use JsonSerializable;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Flooris\Resource\Traits\Make;
use Spatie\QueryBuilder\QueryBuilder;
use Kirschbaum\PowerJoins\PowerJoins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Flooris\Resource\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Flooris\Resource\Collections\Filters;
use Flooris\Resource\Interfaces\Makeable;
use Flooris\Resource\Filters\SearchFilter;
use Spatie\QueryBuilder\QueryBuilderRequest;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Flooris\Resource\Filters\TrashedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\Relation;

class IndexResource implements JsonSerializable, Arrayable, Makeable
{
    use Make;

    public static string $subjectClass;
    public static array $perPageOptions = [25, 50, 100];

    public mixed $subject;
    public Request $request;

    protected Fields $fields;
    protected Filters $filters;
    protected array $additional = [];

    protected LengthAwarePaginator $result;

    public function __construct(mixed $subject = null, ?Request $request = null)
    {
        $this->initializeSubject($subject)
            ->initializeRequest($request)
            ->initializeFields()
            ->initializeFilters();
    }

    protected function initializeSubject(mixed $subject): static
    {
        $this->subject = $subject ?? (isset(static::$subjectClass)
                ? new static::$subjectClass()
                : throw new InvalidArgumentException('Either subject or subjectClass should be set'));

        if (! $this->subjectUsesPowerJoins()) {
            throw new InvalidArgumentException('The subject model should use the PowerJoin trait');
        }

        return $this;
    }

    protected function initializeRequest(?Request $request): static
    {
        $this->request = $request ?? app(Request::class);

        return $this;
    }

    protected function initializeFields(): static
    {
        $model = $this->getModel();

        $this->fields = Fields::make($this->fields())
            ->resolveVisible($model)
            ->resolveQualifiedAttributes($model);

        return $this;
    }

    protected function initializeFilters(): static
    {
        $this->filters = Filters::make($this->filters());

        if ($this->subjectUsesSoftDeletes()) {
            $this->filters->push(TrashedFilter::make());
        }

        if ($this->fields->getSearchable()->isNotEmpty()) {
            $this->filters->push(SearchFilter::make($this->fields->getSearchable()
                ->getQualifiedAttributes()
                ->toArray()));
        }

        return $this;
    }

    public function fields(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function additional(array $additional): static
    {
        $this->additional = $additional;

        return $this;
    }

    public function resolve(): void
    {
        $this->result = $this->query();
    }

    protected function query()
    {
        $query = Querybuilder::for($this->subject, $this->request);

        $this->fields->getUniqueRelations()->each(function (string $relation) use ($query) {
            $query->leftJoinRelationship($relation);
        });

        return $query
            ->allowedFilters($this->filters->getAllowedFilters()->toArray())
            ->allowedSorts($this->fields->getAllowedSorts()->toArray())
            ->with($this->fields->getUniqueRelations()->toArray())
            ->withCount($this->fields->getRelationsToCount()->toArray())
            ->jsonPaginate()
            ->through(fn (mixed $resource) => [
                'resource' => $resource,
                'fields'   => $this->fields->resolve($resource)->keyByName()->toArray(),
            ]);
    }

    protected function getModel(): mixed
    {
        return match (true) {
            $this->subject instanceof Model => $this->subject,
            $this->subject instanceof Builder => $this->subject->getModel(),
            $this->subject instanceof Relation => $this->subject->getRelated(),
            default => null,
        };
    }

    protected function subjectUsesSoftDeletes(): bool
    {
        $model = $this->getModel();

        return $model && in_array(SoftDeletes::class, class_uses_recursive($model), true);
    }

    protected function subjectUsesPowerJoins(): bool
    {
        $model = $this->getModel();

        return $model && in_array(PowerJoins::class, class_uses_recursive($model), true);
    }

    protected function pagination(): array
    {
        return [
            'currentPage'    => $this->result->currentPage(),
            'from'           => $this->result->firstItem(),
            'perPage'        => $this->result->perPage(),
            'perPageOptions' => static::$perPageOptions,
            'lastPage'       => $this->result->lastPage(),
            'to'             => $this->result->lastItem(),
            'total'          => $this->result->total(),
        ];
    }

    protected function requestParameters(): array
    {
        $request = QueryBuilderRequest::fromRequest($this->request);
        $filters = $request->filters();

        if (! $filters->has('search') && $this->filters->keyByName()->has('search')) {
            $filters->put('search', null);
        }

        return [
            'page'   => $request->input('page', new stdClass),
            'sort'   => $request->sorts(),
            'filter' => $filters,
        ];
    }

    public function toArray(): array
    {
        $this->resolve();

        return array_merge([
            'collection' => $this->result->getCollection(),
            'columns'    => $this->fields->resolveLabels($this->getModel())->toColumn(),
            'pagination' => $this->pagination(),
            'filters'    => $this->filters->resolve($this->request)->keyByName(),
            'request'    => $this->requestParameters(),
        ], $this->additional);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toResponse($request): array
    {
        return $this->toArray();
    }
}
