<?php

namespace Modules\Core\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    private $dataResource;
    public function __construct($resource, $dataResource)
    {
        parent::__construct($resource);
        $this->dataResource = $dataResource;
    }

    public function toArray($request)
    {
        return [
            'current_page'    => $this->currentPage(),
            'data'            => $this->dataResource,
            'first_page_url'  => $this->url(1),
            'from'            => $this->firstItem(),
            'last_page'       => $this->lastPage(),
            'last_page_url'   => $this->url($this->lastPage()),
            'links'           => $this->linkCollection(),
            'next_page_url'   => $this->nextPageUrl(),
            'path'            => $this->path(),
            'per_page'        => $this->perPage(),
            'prev_page_url'   => $this->previousPageUrl(),
            'to'              => $this->lastItem(),
            'total'           => $this->total(),
        ];
    }
}
