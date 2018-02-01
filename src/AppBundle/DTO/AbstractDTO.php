<?php

namespace AppBundle\DTO;

abstract class AbstractDTO
{
    /**
     * @param array $item
     */
    public function __construct($item)
    {
        $this->fetchData($item);
    }

    /**
     * Convert self to array.
     *
     * @return array
     */
    public function toArray()
    {
        $returnArray = [];
        foreach ($this->getFieldsList() as $key => $field) {
            $returnArray[$key] = $this->$field;
        }

        return $returnArray;
    }

    /**
     * @param array $item
     */
    protected function fetchData(array $item)
    {
        foreach ($this->getFieldsList() as $key => $field) {
            if (array_key_exists($key, $item)) {
                $this->$field = $item[$key];
            }
        }
    }

    /**
     * Return fields mapping.
     *
     * @return array
     */
    abstract protected function getFieldsList();
}
