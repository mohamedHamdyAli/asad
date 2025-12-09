<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'image',
        'page',
        'is_enabled',
    ];


    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    public function getNameAttribute($value)
    {
        return json_decode($value);
    }
    public function getDescriptionAttribute($value)
    {
        return json_decode($value);
    }
    public static function allActive($isEnable = false)
    {
        if ($isEnable === true) {
            return self::where('is_enabled', 1)->get();
        }
        return self::get();
    }

    public static function getSliders($page, $typeOfQuery = 'get', ...$params)
    {
        $allowedTypes = ['get', 'first', 'paginate', 'count', 'pluck'];

        if (!in_array($typeOfQuery, $allowedTypes, true)) {
            $typeOfQuery = 'get';
        }

        $query = static::where('page', $page)
            ->where('is_enabled', 1);

        switch ($typeOfQuery) {

            case 'paginate':
                $perPage = $params[0] ?? 15;
                $result = $query->paginate($perPage);
                return $result->total() > 0 ? $result : null;

            case 'first':
                $result = $query->first();
                return $result ?? null;

            case 'count':
                return $query->count();

            case 'pluck':
                $column = $params[0] ?? null;
                $key = $params[1] ?? null;

                if ($column === null) {
                    throw new \InvalidArgumentException('pluck requires at least 1 parameter.');
                }

                $result = $key
                    ? $query->pluck($column, $key)
                    : $query->pluck($column);

                return $result->isNotEmpty() ? $result : null;

            case 'get':
            default:
                $result = $query->get();
                return $result->isNotEmpty() ? $result : null;
        }
    }
}
