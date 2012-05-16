<?php

class Ewind_Hohlik_Unit {
    /**
     *
     * @var Ewind_Hohlik_Environment
     */
    private $oHohlikVille ;
    private $sName ;
    private $iAge = 0 ; /*will start from 1 on first wakeUp*/
    private $bInvalid ;
    private $iLastReprAge ;
    private $bSleep ;

    function __construct($hohlikVilleObj) {
        if (is_a($hohlikVilleObj, 'Ewind_Hohlik_Environment')) {
            /* @var $hohlikVilleObj Ewind_Hohlik_Environment */
            $this->oHohlikVille = $hohlikVilleObj ;
            $namer = $hohlikVilleObj->oNamer ;
            /* @var $namer Ewind_NameCraft_Crafter */
            $this->sName = $namer->createName(2) ;
            $this->bInvalid = FALSE ;
            if (mt_rand(1, 100) <= $hohlikVilleObj->fInvalidRate) {
                $this->bInvalid = TRUE ; /* set xx% of invalides*/
            }
            $this->iLastReprAge = 0 ;
        }
        else {
            exit('Hohliks can not live in Spase') ;
        }

    }
    public function getName() {
        return $this->sName ;
    }
    public function getHealth() {
        return $this->bInvalid ;
    }
    public function getAge() {
        return $this->iAge ;
    }

    public function canAct() {
        if ((!$this->bSleep) && (!$this->bInvalid) && (mt_rand(0, 1))) {
            return TRUE ;
        }
        return FALSE ;
    }
    public function canFuck() {
        if ($this->iAge >= $this->oHohlikVille->iGrowDays) {
            return TRUE ;
        }
        return FALSE ;
    }
    public function canReproduce() {
        if ($this->iAge - $this->iLastReprAge >= $this->oHohlikVille->iMinReprRest) {
            return TRUE ;
        }
        return FALSE ;
    }

    public function wasFucked() {
        $this->bSleep = TRUE ;
        return $this->sName;
    }
    public function wasReprodusing() {
        $this->bSleep = TRUE ;
        $this->iLastReprAge = $this->iAge ;
        return $this->sName;
    }

    private function makeAction($actType) { /* $actType = (0 - fuck || 1 - reproduse)*/
        $partners = '' ;
        $mustAct = ($actType ? 2 : 1) ; /*fuck with 1 buddy, reproduse with 2 buddy*/
        $allCanAct = TRUE ;
        $allCanFuck = TRUE ;
        $allCanRep = TRUE ;
        for ($i = 0 ; $i < $mustAct ; $i++) { /*find needed amoun of random buddy and check them*/
            $key = array_rand($this->oHohlikVille->aHohliksList);
            $buddy[$i] = $this->oHohlikVille->aHohliksList[$key] ;/* @var $buddy Ewind_Hohlik_Unit */
            if (!$buddy[$i]->canAct()) $allCanAct = FALSE ;
            if (!$buddy[$i]->canFuck()) $allCanFuck = FALSE ;
            if (!$buddy[$i]->canReproduce()) $allCanRep = FALSE ;
            if ($buddy[$i]->getName() == $this->getName())  $allCanRep = FALSE ;
        }
        /*make list of partners if all OK*/
        if ((!$actType) && $allCanAct && $allCanFuck) {
            for ($i = 0 ; $i < $mustAct ; $i++) {
                $partners .= $buddy[$i]->wasFucked() . ', ';
            }
        }
        if ($actType && $allCanAct && $allCanRep)  {
            for ($i = 0 ; $i < $mustAct ; $i++) {
                $partners .= $buddy[$i]->wasReprodusing() . ', ';
            }
        }
        return $partners ;
    }

    public function live() {
        $action = '' ;
        if (!$this->canAct() && !$this->canFuck()) return ;
        $action .= $this->makeAction(0) ; /*try fuck, get partners names if sucsess*/
        if ($action) {
            $this->wasFucked() ;
            return $this->sName . ', ' . $action . ' - потрахались.<br />' ; /*was fucking - exit*/
        }
        if (!$this->canReproduce()) return ;
        $action .= $this->makeAction(1) ; /*if not fuck(not exit) try reprodusing , get partners names if sucsess*/
        if ($action) {
            /*breed 1 to MaxReprRate hohliks*/
            $numNewBreed = mt_rand(1, $this->oHohlikVille->iMaxReprRate) ;
            $this->oHohlikVille->addHohliks($numNewBreed) ;
            $this->wasReprodusing() ;
            return $this->sName . ', ' . $action . ' - сделали новых хохликов: ' . $numNewBreed . '<br />' ;
        }
    }

    public function wakeUp() {
        $this->iAge++ ;
        $this->bSleep = FALSE ;
    }
         function __destruct() {
         //    echo ", $this->iAge" ;
     }

}
?>
