<?php

declare(strict_types=1);

namespace Core;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Exception;

abstract class AbstractModel extends Model implements ModelInterface
{
    protected ?string $primaryKey = null;

    /**
     * @param string $name
     * @return $this
     */
    public function setPrimaryKey(string $name): static
    {
        $this->primaryKey = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey(): mixed
    {
        return $this->primaryKey;
    }

    /**
     * @return void
     */
    public function validation(): void
    {
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (str_starts_with($method, 'set')) {
            $property = $this->getPropertyNameByCall($method, 3);
            if (property_exists($this, $property)) {
                $this->$property = $arguments[0];
                return $this;
            }
        } elseif (str_starts_with($method, 'get')) {
            $property = $this->getPropertyNameByCall($method, 3);
            if (property_exists($this, $property)) {
                return $this->$property;
            }
        }

        return parent::__call($method, $arguments);
    }

    /**
     * @param string $method
     * @param int $methodStartStrLen
     * @return string
     */
    private function getPropertyNameByCall(string $method, int $methodStartStrLen): string
    {
        return ltrim(strtolower(
            preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', substr($method, $methodStartStrLen))
        ), '_');
    }
}
