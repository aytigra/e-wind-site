<?php
/*
 * Class Rainbow
 */
class Ewind_Rainbow_Crafter
{
    private $aSize; //(width , height)
    private $aGradColors;
    private $sFilePath;
    private $sOutType;
    private $iBlurLevel;
    private $aNormalRainbow;
    private $aRGBcode;

    function __construct($initSize, $initType, $initFilePath = ''){
	$this->aSize = $initSize;
	$this->sOutType = $initType;
	if ($initType != ('html'||'png')) {
	    echo '<p class="error">Error: you must initialise output type for Rainbow, "html" or "png"</p>';
	    exit();
	}
	if (($initType == 'png') && (empty($initFilePath))) {
	    echo '<p class="error">Error: you must initialise path of image file for Rainbow</p>';
	    exit();
	}
	$this->sFilePath = $initFilePath;
	$this->aNormalRainbow = array('�', '�', '�', '�', '�', '�', '�');
	$this->aRGBcode = array( //������� ������������ ������ ������ � RBG �����
	    '�' => array(255, 0, 0),
	    '�' => array(255, 153, 0),
	    '�' => array(255, 255, 0),
	    '�' => array(0, 255, 0),
	    '�' => array(0, 192, 255),
	    '�' => array(0, 0, 255),
	    '�' => array(102, 0, 204));
	$this->aGradColors = $this->aNormalRainbow;
	$this->iBlurLevel = 0;
    }

    function setSize($size) {
	$this->aSize = $size;
    }
    function getSize() {
	return $this->aSize;
    }

    function setColors($colors) {
	$this->aGradColors = array();
	if (!is_array($colors)) { //���� ������, �� ���������� �� ��� ����� ������
	    $colorList = '';
	    foreach ($this->aNormalRainbow as $color) $colorList .= $color;
	    $colorList = '/['.$colorList.']/';
	    for ($i=0; $i < strlen($colors); $i++){
		if (preg_match($colorList, $colors[$i])){
		    $this->aGradColors[] = $colors[$i];
		}
	    }
	}
	else $this->aGradColors = $colors;
	if (count($this->aGradColors)<1){
	    $this->resetColors ();
	    return '<p class="error">Error: ����� � ������ ������</p>';
	}
	if (count($this->aGradColors)>40){
	    $this->resetColors ();
	    return '<br /><p class="error">Error: ���-�� ������ ������ ������...</p>';
	}

    }
    function getColors() {
	return $this->aGradColors;
    }

    function setBlurLevel($level) {
	$this->iBlurLevel = $level;
    }
    function getBlurLevel() {
	return $this->iBlurLevel;
    }


    function resetColors() {
	$this->aGradColors = array();
	$this->aGradColors = $this->aNormalRainbow;
    }

    function reverseRainbow() {
	$this->aGradColors = array_reverse($this->aGradColors);
    }

    function shuffleRainbow() {
	shuffle($this->aGradColors);
    }

    function blurColors($inGrad) {
	$outGrad = array();
        $prev = '' ;
	foreach ($inGrad as $key=>$color){
	    if ($prev){ //���� ���� ��� �������� �����, ��
		foreach ($color as $index => $val) {
		    $outGrad["$prev$key"][$index] = round(($val + $inGrad[$prev][$index]) / 2);
		}//��������� ������� �������������� ������� ������ �����
	    }
	    $outGrad[$key] = $color;
	    $prev = $key;
	}
	return $outGrad;
    }

    function createRainbow() {
	//������� ������ ���������� �� ������ ��������� �������
	$i=1;
	foreach ($this->aGradColors as $key){
	    $gradCode["$key$i"] = $this->aRGBcode[$key];
	    $i++; //�������� - ����� �� �������� ���������� �����
	}
	for ($i=1; $i <= $this->iBlurLevel; $i++){//�������� �������� � ������������ � �������� �������
	    $gradCode = $this->blurColors($gradCode);
	}
	if ($this->sOutType == 'html') {//������ HTML � CSS ���� ������
	    $htmlRainbow = array();
	    $htmlRainbow['html']= '<table id="rainbow"><tr>';
	    $htmlRainbow['css'] = '<style type = "text/css"> #rainbow'.
		    '{width: '.$this->aSize[0].'px; height: '.$this->aSize[1].
		    'px; border-collapse: collapse;} ';
	    foreach ($gradCode as $key => $color) {
		$rgb = 'rgb('.$color[0].', '.$color[1].', '.$color[2].')';
		$htmlRainbow['html'] .= '<td id="'.$key.'"></td>';
		$htmlRainbow['css'] .= ' #rainbow #'.$key.' {background: '.$rgb.';} ';
	    }
	    $htmlRainbow['html'] .= ' </tr></table>';
	    $htmlRainbow['css'] .= ' </style>';
	    return $htmlRainbow;
	}
	if ($this->sOutType == 'png') {//������ ����������� ������ � �������� � ����
	    $img = imagecreatetruecolor($this->aSize[0], $this->aSize[1]);
	    $colorWidth = $this->aSize[0]/count($gradCode);
	    $i=0;
	    foreach ($gradCode as $key2 => $color2 ){
		$graphColor = imagecolorallocate($img, $color2[0], $color2[1], $color2[2]);
		imagefilledrectangle($img, $colorWidth*$i, 0, $colorWidth*($i +1), $this->aSize[1], $graphColor);
		$i++;
	    }
	    imagepng($img, $this->sFilePath, 5);
	    imagedestroy($img);
	}
    }
}
?>
