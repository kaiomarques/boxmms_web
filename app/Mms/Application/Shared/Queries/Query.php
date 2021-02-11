<?php namespace App\Mms\Application\Shared\Queries;

use App\Mms\Shared\Tempo;

abstract class Query
{
    protected const DATE_FORMAT = "Y-m-d";
    private const DATE_TIME_FORMAT = "Y-m-d H:i:s";

    protected function parsePositiveInt($int)
    {
        return $this->parseInt($int, 1);
    }

    protected function parseNonNegativeInt($int)
    {
        return $this->parseInt($int, 0);
    }

    protected function parseInt($int, $minValue) 
    {
        $result = isset($int) && !empty($int) && is_numeric($int) ? intval($int) : 0;
        
        return $result >= $minValue ? $result : null;
    }

    protected function parseStringOr($string, $fallback)
    {
        $string = $this->parseString($string);

        return $string == null ? $fallback : $string;
    }

    protected function parseString($string)
    {
        $string = trim($string);

        return isset($string) && !empty($string) ? $string : null;
    }
    
    protected function parseDateOr($date, $fallback, Tempo $complemento = null)
    {
        $result = $this->parseDate($date, $complemento);
        
        return $result == null ? $this->parseDate($fallback, $complemento) : $result;
    }
    
    protected function parseDate($date, Tempo $complemento = null)
    {
        $complemento = $complemento == null ? Tempo::primeiroSegundoDoDia() : $complemento;

        $result = \DateTime::createFromFormat(self::DATE_TIME_FORMAT, "{$date} {$complemento->toString()}");
        
        if ($result === false)
            return null;

        return $result;
    }
}