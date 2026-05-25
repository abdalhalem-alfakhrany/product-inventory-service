<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "success" => true,
            "data" => $this->collection,
            "meta" => [
                'pagination' => [
                    'total' => $this->total(),
                    'per_page' => $this->perPage(),
                    'current_page' => $this->currentPage(),
                    'last_page' => $this->lastPage(),
                    'next' => $this->nextPageUrl(),
                    'prev' => $this->previousPageUrl(),
                ]
            ],
        ];
    }
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [];
    }
}
