<?php
/**
 * Created by PhpStorm.
 * User: phpartisan[dot]ir
 * Date: 7/23/21
 * Time: 14:27
 */

class CodalSearchFilter
{
    public $params;

    public function __construct()
    {
        $this->params = AppConfig::DefaultSearchParams();
    }

    public function NotAudited(bool $isActive = true)
    {
        $this->params['NotAudited'] = ($isActive)?'true':'false';

        return $this;
    }

    public function Audited(bool $isActive = true)
    {
        $this->params['Audited'] = ($isActive)?'true':'false';

        return $this;
    }

    public function Publisher(bool $isActive = false)
    {
        $this->params['Publisher'] = ($isActive)?'true':'false';

        return $this;
    }

    public function OnlyChild(bool $isActive = true)
    {
        $this->params['Childs'] = ($isActive)?'true':'false';

        return $this;
    }

    public function OnlyMain(bool $isActive = true)
    {
        $this->params['Mains'] = ($isActive)?'true':'false';

        return $this;
    }

    public function CompanyStatus(int $status = -1)
    {
        if ($status < -1 || $status > 5){throw new \Exception('Company status number is false');}

        $this->params['CompanyStatus'] = $status;

        return $this;
    }

    public function CompanyType(int $status = -1)
    {
        if ($status != -1 && $status != 1){throw new \Exception('Company type number is false');}

        $this->params['CompanyType'] = $status;

        return $this;
    }

    public function Category(int $status = -1)
    {
        if ($status < -1 || $status > 11){throw new \Exception('Category number is false');}

        $this->params['Category'] = $status;

        return $this;
    }

    public function LetterType(int $status = -1)
    {
        $this->params['LetterType'] = $status;

        return $this;
    }

    public function LetterCode(int $status = 0)
    {
        $this->params['LetterCode'] = $status;

        return $this;
    }

    public function Length(int $status = -1)
    {
        if ($status < -1 || $status > 12){throw new \Exception('length number is false');}

        $this->params['Length'] = $status;

        return $this;
    }

    public function Subject(string $subject = null)
    {
        $this->params['Subject'] = $subject;

        return $this;
    }

    public function TrackingNo(int $number = 0)
    {
        $this->params['TrackingNo'] = $number;

        return $this;
    }

    public function FromDate()
    {

    }

    public function ToDate()
    {

    }
}