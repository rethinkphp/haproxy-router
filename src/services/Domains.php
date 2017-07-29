<?php

namespace rethink\hrouter\services;

use rethink\hrouter\models\Domain;
use Illuminate\Database\Eloquent\Builder;
use rethink\hrouter\support\ValidationException;

/**
 * Class Domains
 *
 * @package rethink\hrouter\services
 */
class Domains extends ModelService
{
    public $modelClass = Domain::class;

    public function load($id, array $options = [])
    {
        if ($id instanceof Domain) {
            return $id;
        }

        return Domain::query()
            ->where(function (Builder $query) use ($id) {
                $query
                    ->orWhere('id', $id)
                    ->orWhere('name', $id)
                ;
            })
            ->first();
    }

    public function queryAll(array $params = [])
    {
        return Domain::query()->get();
    }

    /**
     * Create a new domain.
     *
     * @param array $attributes
     * @return Domain
     * @throws ValidationException
     */
    public function create(array $attributes = [])
    {
        $attributes['id'] = uniqid();

        $validator = validate($attributes, [
            'name' => 'required|unique:domains',
        ]);

        if ($validator->fails()) {
            throw ValidationException::fromValidator($validator);
        }

        $domain = new Domain($attributes);
        $domain->save();

        return $domain;
    }

    public function update($domain, array $attributes)
    {
        $domain = $this->loadOrFail($domain);

        $domain->fill($attributes);
        $domain->save();

        return $domain;
    }

    public function delete($domain)
    {
        $domain = $this->loadOrFail($domain);

        // TODO used domain can not be deleted

        return $domain->delete();
    }
}