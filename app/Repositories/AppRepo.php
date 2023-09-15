<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;

abstract class AppRepo
{
    /**
     * Eloquent model instance.
     */
    protected $model;

    /**
     * load default class dependencies.
     *
     * @param Model $model Illuminate\Database\Eloquent\Model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * get all the items collection from database table using model.
     *
     * @param int $after
     * @param int $limit
     * @param string $orderBy
     * @param string $order
     * @return Collection of items.
     */
    public function get(int $after = 0, int $limit = 15, string $orderBy = 'id', string $order = 'desc'): Collection
    {
        $query = $this->model;
        if ($after) {
            $query->where('id', $after);
        }
        $query->limit($limit);
        $query->orderBy($orderBy, $order);

        return $query->get();
    }

    /**
     * get collection of items in paginate format.
     *
     * @param int $limit
     * @return Paginator|null of items.
     */
    public function paginate(int $limit = 10): Paginator
    {
        return $this->model->paginate($limit);
    }

    /**
     * create new record in database.
     *
     * @param array $data
     * @return model object with data.
     */
    public function store(array $data): Model
    {
        $item = new $this->model;
        $item->fill($data);
        $item->save();

        return $item;
    }

    /**
     * update existing item.
     *
     * @param Integer $id integer item primary key.
     * @param $data
     * @return Model updated item object.
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->model->findOrFail($id);
        $item->fill($data);
        $item->save();

        return $item;
    }

    /**
     * get requested item and send back.
     *
     * @param  Integer $id: integer primary key value.
     * @return Model requested item data.
     */
    public function show($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Delete item by primary key id.
     *
     * @param $id: primary key id.
     * @return boolean
     */
    public function delete($id): bool
    {
        return $this->model->destroy($id);
    }
}