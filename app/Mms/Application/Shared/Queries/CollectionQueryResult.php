<?php namespace App\Mms\Application\Shared\Queries;

abstract class CollectionQueryResult
{
    private $items = array();

    public function __construct(array $items) 
    {
        foreach($items as $item) {
            array_push($this->items, $this->handle($item));
        }
    }

    public function items() {
        $result = array();

        foreach($this->items as $item) {
            array_push($result, $item);
        }

        return $result;
    }

    public function count()
    {
        return count($this->items);
    }

    abstract protected function handle($item);
}