<?php

namespace AppBundle\DTO;

class AbstractTotalDTO extends AbstractDTO
{
    const TOTAL_SUM = 'total_sum';

    /**
     * @var int
     */
    protected $totalSum;

    /**
     * @var array
     */
    private static $fieldsMapping = [
        'total_sum' => 'totalSum',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $item)
    {
        parent::__construct($item);

        $this->totalSum = (int) $this->totalSum;
    }

    /**
     * Get list of fields keys.
     *
     * @return array
     */
    public static function getFields()
    {
        return array_keys(self::$fieldsMapping);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldsList()
    {
        return self::$fieldsMapping;
    }
}
