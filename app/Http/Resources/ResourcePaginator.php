<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator as PaginatorInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class ResourcePaginator.
 */
class ResourcePaginator extends Paginator
{
    /**
     * JsonResource classname.
     *
     * @var string
     */
    protected string $collectionClass;

    /**
     * Paginator given in the constructor.
     *
     * @var PaginatorInterface
     */
    protected PaginatorInterface $paginator;

    /**
     * @param Paginator|LengthAwarePaginator $paginator
     * @param string $collectionClass
     *
     * @throws Exception
     */
    public function __construct(Paginator|LengthAwarePaginator $paginator, string $collectionClass)
    {
        parent::__construct($paginator->items, $paginator->perPage, $paginator->currentPage, $paginator->options);

        $this->paginator = $paginator;
        $this->collectionClass = $collectionClass;

        if (!is_subclass_of($collectionClass, ResourceCollection::class)) {
            throw new Exception('Invalid collectionClass. It must be an instance of ResourceCollection.');
        }
    }

    /**
     * Get paginator that was passed to the constructor.
     *
     * @return PaginatorInterface
     */
    public function getPaginator(): PaginatorInterface
    {
        return $this->paginator;
    }

    /**
     * Get number of records in the database.
     *
     * @return int|null
     */
    public function total(): ?int
    {
        return $this->paginator instanceof LengthAwarePaginator ? $this->paginator->total() : null;
    }

    /**
     * Get last page number.
     *
     * @return int|null
     */
    public function lastPage(): ?int
    {
        return $this->paginator instanceof LengthAwarePaginator ? $this->paginator->lastPage() : null;
    }

    /**
     * Get collection of items.
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return parent::getCollection();
    }

    /**
     * Get resource collection of items.
     *
     * @return ResourceCollection
     */
    public function getResourceCollection(): ResourceCollection
    {
        return new $this->collectionClass($this->items);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->getResourceCollection();
        $meta = [
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'total' => $this->total(),
            'links' => [
                'first' => $this->url(1),
                'prev' => $this->previousPageUrl(),
                'self' => $this->url($this->currentPage()),
                'next' => $this->nextPageUrl(),
                'last' => $this->url($this->lastPage()),
            ],
        ];

        return compact('data', 'meta');
    }
}
