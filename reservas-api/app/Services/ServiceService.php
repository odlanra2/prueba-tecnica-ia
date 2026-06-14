<?php

namespace App\Services;

use App\Models\Service;
use App\Services\Contracts\ServiceServiceInterface;

class ServiceService implements ServiceServiceInterface
{
    public function all()
    {
        return Service::all();
    }

    public function create(array $data)
    {
        return Service::create($data);
    }

    public function update($id, array $data)
    {
        $service = Service::findOrFail($id);
        $service->update($data);
        return $service;
    }

    public function delete($id)
    {
        $service = Service::findOrFail($id);
        return $service->delete();
    }
}
