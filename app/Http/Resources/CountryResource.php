<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'currency' => $this->currency,
            'capital' => $this->capital,
            'currency_symbol' => $this->currency_symbol,
            'flag' => $this->flag,
            'phonecode' => $this->phonecode,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
