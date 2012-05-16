<?php
/*
 * class for generating names.
 */

class Ewind_NameCraft_Crafter
{
    private $sNameStyle;
    private $aNameStyleDict;
    private $aVowels = array('à','å','¸','è','î','ó','þ','ÿ');
    const LANG_FILES_PATH = 'langstyles';

    public function setNameStyle($style) {
        $error = '' ;
	$pathInfo = pathinfo(__FILE__);
	$fileName = 'namestyle_'.$style.'.txt';
	$styleFilePath = $pathInfo['dirname'] .DIRECTORY_SEPARATOR. self::LANG_FILES_PATH .DIRECTORY_SEPARATOR. $fileName;
	if (($style == ('JP'|'CHI'|'IND') ) && (!file_exists($styleFilePath)))
	    $error='<p class="error">Error: Ïîääåðæêè òàêîãî ÿçûêà íåòó.';
	else {
	    $fileContent = file_get_contents($styleFilePath);
	    $this->sNameStyle = $style;
	    $this->aNameStyleDict = explode("&", $fileContent);
	}
	return $error;
    }

    public function getNameStyle() {
	return $this->sNameStyle;
    }

    /*Ïðîâåðÿåòcvt ñìåæíûå áóêâû ñëîãîâ, îíè îäíîãî òèïà(ãîàñíûå, ñîãëàñíûå) èëè ðàçíîãî */
    private function checkSyllableConnect($prevSyll, $nextSyll, $checkType) {
	$prevChar = $prevSyll[strlen($prevSyll)-1];
	$nextChar = $nextSyll[0];
	$check = FALSE;
	if (($checkType == 'opposite') && ((in_array($prevChar, $this->aVowels))
		xor (in_array($nextChar, $this->aVowels)))){
	    $check =TRUE;
	}
	if (($checkType == 'same') && (!((in_array($prevChar, $this->aVowels))
		xor (in_array($nextChar, $this->aVowels))))){
	    $check =TRUE;
	}
	return $check;
    }

    public function createName($nameLength) {
	$key = array_rand($this->aNameStyleDict);
	$name = $this->aNameStyleDict[$key];
	$syllabeCount = 1;
	while ($syllabeCount<$nameLength){
	    $key = array_rand($this->aNameStyleDict);
	    $syllable = $this->aNameStyleDict[$key];
	    if ($this->sNameStyle == 'JP') {
			if ($this->checkSyllableConnect($name, $syllable, 'opposite')){
				$name .= $syllable; $syllabeCount++;
			}
	    }
	    if ($this->sNameStyle == 'CHI') {
			if ($syllabeCount == 1) $name .= ' ' ;
		    $name .= $syllable. ' '; $syllabeCount++;
	    }
	    if ($this->sNameStyle == 'IND') {
			if ($this->checkSyllableConnect($name, $syllable, 'opposite')){
				$name .= $syllable; $syllabeCount++;
			}
	    }
	}
	$name[0] = strtr( $name[0], 'éöóêåíãøùçõúôûâàïðîëäæýÿ÷ñìèòüáþ¸', 'ÉÖÓÊÅÍÃØÙÇÕÚÔÛÂÀÏÐÎËÄÆÝß×ÑÌÈÒÜÁÞ¨');
	return $name;
    }
}

?>
