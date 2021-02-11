<?php
namespace App\Cqrs\Queries\Application\Shared;

abstract class QueriesHandler
{
    private $handlers = array();

    public function __construct()
    {        
        $this->registerHandlers();
    }

    protected function register($class, \Closure $handler)
    {
        $this->handlers[$class] = $handler;

        return $this;
    }
    
    public function Handle($query)
    {
        foreach ($this->handlers as $class => $handler) {
            if (get_class($query) == $class) {
                return $handler($query);
            }
        }

        return null;
    }

    protected abstract function registerHandlers();
}