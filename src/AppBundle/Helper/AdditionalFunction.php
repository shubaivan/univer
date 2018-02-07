<?php

namespace AppBundle\Helper;

class AdditionalFunction
{
    /**
     * @param $date
     *
     * @throws \Exception
     *
     * @return \DateTime|false
     */
    public function validateDateTime($date)
    {
        $checkResult = false;
        $dateTimeClass = \DateTime::createFromFormat('d.m.Y', $date);
        if ($dateTimeClass) {
            $checkResult = $dateTimeClass->format('d.m.Y') == $date;
        }

        if (!$dateTimeClass instanceof \DateTime || !$checkResult) {
            throw new \Exception('Date fields must be format \'dd.mm.yy\'');
        }

        return $dateTimeClass;
    }
}
