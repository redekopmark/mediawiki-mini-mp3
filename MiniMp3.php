<?php
 
$wgHooks['ParserFirstCallInit'][] = 'wfMp3';
$wgMediaHandlers['audio/mp3'] = 'MiniMp3Handler';
$wgMediaHandlers['audio/mpeg'] = 'MiniMp3Handler';
$wgFileExtensions[] = 'mp3';

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'MiniMp3',
	'description' => 'Uses a very small flash player to stream your mp3 files',
	'author' => 'Reddo',
	'version' => '0.3',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MiniMp3'
);
 
function wfMp3( Parser &$parser ) {
	$parser->setHook('mp3', 'renderMp3');
	$parser->setHook('audio///mpeg', 'renderMp3');
	$parser->setHook('mpeg', 'renderMp3');
	return true;
}

# The callback function for converting the input text to HTML output
function renderMp3( $input, $params ) {
	global $wgScriptPath;
	$output= '';
	
	//get params
	//if no color param given for specific element default to general color param
	//if no general color param given default to 50A6C2
	$Color = isset( $params['color'] ) ? $params['color'] : '50A6C2';
	if ( $Color == '') { $Color = '50A6C2'; }
	
	$Played = isset( $params['played'] ) ? $params['played'] : 'CCCCCC';
	if ( $Played == '') { $Played = 'CCCCCC'; }

	//File uploaded or external link ?
	$img = wfFindFile($input);
	if (!$img) { 
		$mp3 = $input;
	} 
	else { 
		$mp3 = $img->getFullURL();
	}
	
	unset($img);
	
		$output = '<audio src="'.$mp3.'" color="#'.$Color.'" played="#'.$Played.'"></audio>';
		return $output;
}

class MiniMp3Handler extends MediaHandler {
var $buttColor, $slidColor, $loadColor, $backgroundCode, $bg;

	function validateParam( $name, $value ) { return true; }
	function makeParamString( $params ) { return ''; }
	function parseParamString( $string ) { return array(); }
	function normaliseParams( $file, &$params ) { return true; }
	function getImageSize( $file, $path ) { return false; }

	function getParamMap() {
		return array(
//			'mp3_color' => 'color',
//			'mp3_slidecolor' => 'slidcolor',
//			'mp3_loadcolor' => 'loadcolor',
//			'mp3_buttoncolor' => 'buttoncolor',
//			'mp3_backColor' => 'bg',
		);
	}

	# Prevent "no higher resolution" message.
	function mustRender( $file ) { return true; }

	function doTransform ( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		return new Mp3Output( $this->getParamMap (), $file->getFullUrl () );
	}
}

class Mp3Output extends MediaTransformOutput {
var $mp3, $Color, $played;

	function __construct( $params, $mp3 ){
		$this->Color = isset( $params['color'] ) ? $params['color'] : '50A6C2';
		if ( $this->Color == '') { $this->Color = '50A6C2'; }
		
		$this->Played = isset( $params['played'] ) ? $params['played'] : 'CCCCCC';
		if ( $this->Played == '') { $this->Played = 'CCCCCC'; }

		$this->mp3 = $mp3;
	}

	function toHtml( $options=array () ) {
		$output = '<audio src="'.$this->mp3.'" color="#'.$this->Color.' played="#'.$this->Played.'"></audio>';
		return $output;
	}
}
