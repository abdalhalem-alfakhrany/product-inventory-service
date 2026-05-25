<?php

namespace App\Models;

use App\Enum\ProductStatus;
use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Nonstandard\Uuid;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $casts = ['status' => ProductStatus::class];
    protected $keyType = 'string';
    public $increment = false;

    public function newUniqueId(): string
    {
        return Uuid::uuid7()->toString();
    }


    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock_quantity', '<', 'low_stock_threshold');
    }
}
