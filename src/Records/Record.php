<?php

declare(strict_types=1);

namespace Avant\ZohoCRM\Records;

use ArrayAccess;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Support\Collection;
use JsonSerializable;

class Record implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    protected array $attributes = [];
    protected array $original = [];
    protected array $changes = [];

    public function __construct(iterable $attributes = [])
    {
        $this->fill($attributes);
        $this->syncOriginal();
    }

    public function __get(string $name)
    {
        throw_unless(
            array_key_exists($name, $this->attributes),
            new Exception("Attribute {$name} not found")
        );

        return $this->attributes[$name];
    }

    public function fill(iterable $attributes): static
    {
        $this->attributes = array_merge($this->attributes, static::normalizeArray($attributes));

        return $this;
    }

    public function syncOriginal(): static
    {
        $this->original = $this->attributes;

        return $this;
    }

    public function forgetOriginal(): static
    {
        $this->original = [];

        return $this;
    }

    public function getDirty(): array
    {
        return collect($this->attributes)
            ->filter(fn ($v, $k) => !$this->originalIsEquivalent($k))
            ->toArray();
    }

    public function syncChanges(): static
    {
        $this->changes = $this->getDirty();

        return $this;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    protected function originalIsEquivalent($attribute): bool
    {
        return (
                array_key_exists($attribute, $this->attributes)
                && array_key_exists($attribute, $this->original)
                && $this->original[$attribute] === $this->attributes[$attribute]
            ) || (
                !array_key_exists($attribute, $this->attributes)
                && !array_key_exists($attribute, $this->original)
            );
    }

    public static function make(iterable $attributes): static
    {
        $attributes = static::normalizeArray($attributes);

        return new static($attributes);
    }

    private static function normalizeArray($array): array
    {
        $array = Collection::wrap($array)
            ->filter(fn ($_, $k) => !str_starts_with($k, '$'));

        $array
            ->filter(fn ($v) => is_array($v) && data_get($v, 'id'))
            ->each(fn ($v, $k) => $array->put($k, data_get($v, 'id')));

        $array
            ->filter(fn ($v) => is_array($v))
            ->each(fn ($v, $k) => $array->put(
                $k,
                collect($v)
                    ->map(fn ($v) => is_array($v)
                        ? static::normalizeArray($v)
                        : $v)
                    ->toArray()
            ));

        return $array->toArray();
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson($options = 0): false|string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }

        return $json;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
