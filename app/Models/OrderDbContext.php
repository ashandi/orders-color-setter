<?php

namespace App\Models;

interface OrderDbContext
{
    /**
     * @param $key
     * @param array $values
     * @return static
     */
    public function whereIn($key, array $values);

    /**
     * @return mixed
     */
    public function get();
}
