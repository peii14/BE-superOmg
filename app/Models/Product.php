<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SearchableTrait;

    protected $searchable = [
        // column with priorities
        'columns' => [
            'products.name' => 6,
            'products.details' => 3,
            'products.description' => 2,
        ],
    ];

    protected $guarded = [];

    public function scopeMightAlsoLike($query) {
        return $query->inRandomOrder()->take(3);
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag');
    }

    public function toSearchableArray() {
        $array = $this->toArray();
        $extraFields = [
            'category' => $this->category->name
        ];
        return array_merge($array, $extraFields);
    }
}
