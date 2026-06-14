<?php

namespace App\Services\Contracts;

interface ServiceServiceInterface
{
    public function all();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
