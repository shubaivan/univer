<?php

namespace AppBundle\Helper;

class AdditionalFunction
{
    /**
     * @param $date
     * @param mixed $format
     *
     * @throws \Exception
     *
     * @return \DateTime|false
     */
    public function validateDateTime($date, $format)
    {
        $checkResult = false;
        $dateTimeClass = \DateTime::createFromFormat($format, $date);
        if ($dateTimeClass) {
            $checkResult = $dateTimeClass->format($format) === $date;
        }

        if (!$dateTimeClass instanceof \DateTime || !$checkResult) {
            throw new \Exception('Date fields must be format \'dd.mm.yy\'');
        }

        return $dateTimeClass;
    }
}
