<?php

namespace App\Models;

use App\Enum\ProductStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Nonstandard\Uuid;

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
}
