<?php

class Ewind_Hohlik_Environment {
    public $oNamer ;
    public $iMaxAge = 15;
    public $fInvalidRate = 15;
    public $iMaxReprRate = 5;
    public $iMinReprRest = 3;
    public $iGrowDays = 2;
    public $aHohliksList ;
    public $iCurrentDay = 1 ;
    public $iTotalNumberOfHohlics = 0 ;

    public function __destruct() {
        //kill colony or it became autonomic and undestructable, referensed by each hohlik
        $this->aHohliksList = array() ;
    }

    function setHohlikVille($maxLifeTime, $invalidRate, $maxReprRate, $minReprRest, $growDays, $nation) {
	$this->oNamer = new Ewind_NameCraft_Crafter() ; /* @var $this->oNamer Ewind_NameCraft_Crafter */
        $error = '' ;
        $error .= $this->oNamer->setNameStyle($nation) ;
        if (is_numeric($invalidRate) && $invalidRate < 100 && $invalidRate >= 0) {
            $this->fInvalidRate = round($invalidRate) ;
        }
        else $error .= '<br />Доля инвалидов не может быть больше ста и меньше нуля' ;

        if (is_numeric($maxLifeTime) && $maxLifeTime <= 1000000 && $maxLifeTime > 3) {
            $this->iMaxAge = round($maxLifeTime) ;
        }
        else $error .= '<br />Хохлики не могут жить дольше миллиона лет и меньше трех' ;

        if (is_numeric($maxReprRate) && $maxReprRate <= 10 && $maxReprRate >= 0) {
            $this->iMaxReprRate = round($maxReprRate) ;
        }
        else $error .= '<br />Три хохлика не могут сделать больше десяти новых хохликов и меньше нуля' ;

        if (is_numeric($minReprRest) && $minReprRest <= 1000000 && $minReprRest >= 0) {
            $this->iMinReprRest = round($minReprRest) ;
        }
        else $error .= '<br /> Хохликам нужно отдыхть после размножения от нуля до миллиона лет' ;

        if (is_numeric($growDays) && $growDays <= 10 && $growDays >= 0) {
            $this->iGrowDays = round($growDays) ;
        }
        else $error .= '<br />Время роста не может быть больше десяти и меньше нуля' ;

        return $error ;
    }

    public function addHohliks($amount) {
        $error = '';
        if (is_numeric($amount) && $amount <= 100 && $amount > 0) {
            for ($i = 0; $i < $amount; $i++) {
                $this->aHohliksList[] = new Ewind_Hohlik_Unit($this) ;
                $this->iTotalNumberOfHohlics++ ;
            }
        }
        else $error .= '<br />Тусовка не может быть больше сотни и меньше одного хохлика' ;
        return $error ;
    }

    private function liveOneDay() {
        $dailyReport = '' ;
        /*Wake Up all and removing dead*/
        foreach ($this->aHohliksList as $keyh => $hohlik1) {
            /* @var $hohlik1 Ewind_Hohlik_Unit */;
            $hohlik1->wakeUp() ;
            if ($hohlik1->getAge() >= $this->iMaxAge) {
                unset($this->aHohliksList[$keyh]) ;
                $hohlik1 = '' ; // if killed last remove second
            }
        }
        /*turn hohliks to life*/
        foreach ($this->aHohliksList as $k => $hohlik) {
            /* @var $hohlik Ewind_Hohlik_Unit */
            $dailyReport .= $hohlik->live() ;
        }
        return $dailyReport ;
    }


    public function liveForDays($number) {
        $startNumhoh = $this->iTotalNumberOfHohlics ;
        $numHoh = 0;
        $dayX = 'никако';
        if ($number > 365 || $number < 1) {
            return '<br />Ты можешь наблюдать за хохликами от одного до 365 дней' ;
        }
        $report = '' ;
        $stat ='' ;
        $totalFucks = 0 ;
        $totalRerp = 0 ;
        for ($j = 0 ; $j < $number ; $j++) {
            if ($numHoh > 300) {
                return 'На '. ++$j .'-й день хохликов стало больше трехсот и они свалили в места попросторней';
            }
            $dailyReport = $this->liveOneDay() ;
            $fuckCount = substr_count($dailyReport, 'потрахались') ;
            $reprCount = substr_count($dailyReport, 'сделали') ;
            $numHoh = count($this->aHohliksList) ;
            if ($j < 40 || ($j > ($number - 4))) { /* show report only for first 40 and last 3 days*/
                $report .= '<p>' . $this->iCurrentDay . ' day report:<br />'
                        /*show action list only if less than 40 hohliks and for first 20 days*/
                        . (($numHoh <40 && $this->iCurrentDay < 21 ) ? $dailyReport : '')
                        . 'Живет хохликов: ' . $numHoh . ' <br />'
                        . 'Было потрахов: ' . $fuckCount . ' <br />'
                        . 'Было размножений: ' . $reprCount . ' </p>';
            }
            $this->iCurrentDay++ ;
            $totalFucks += $fuckCount ;
            $totalRerp += $reprCount ;
            if ($dayX == 'никако' && ($this->iTotalNumberOfHohlics >= ($startNumhoh*2))) {
                $dayX = $j + 1;
            }
        }
        $stat .= '<p><strong><br /> Всего было:'
                . '<br />' . $this->iTotalNumberOfHohlics . ' хохликов.'
                . '<br />' . $totalFucks . ' потрахов.'
                . '<br />' . $totalRerp . ' размножений.'
                . ($dayX != 'никако' ? '<br />На ' . $dayX . 'й день количество хохликов увеличилось больше чем вдвое.' : '' )
                . '</strong></p>' ;
        return $stat . $report ;
    }

}
?>
