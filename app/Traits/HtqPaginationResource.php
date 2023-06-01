<?php
declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Arr;

trait HtqPaginationResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'payload' => !$this->collection->isEmpty() ? $this->metronicPaginated($request) : []
        ];
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function metronicPaginated($request)
    {
        $paginated = $this->resource->toArray();

        $default = [
            'pagination' => $this->meta($paginated),
        ];

        if (method_exists($this->resource, 'paginationInformation')) {
            return $this->resource->paginationInformation($request, $paginated, $default);
        }

        return $default;
    }

    /**
     * Get the pagination links for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function paginationLinks($paginated)
    {
        return $paginated['links'];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'path',
            // 'first_page_url',
            // 'last_page_url',
            // 'prev_page_url',
            // 'next_page_url',
        ]);
    }
}
