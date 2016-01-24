<?php
class Generic {

	//This class is used for rendering templates, other than index.html, in Twig.

	public static function aboutGoatTeam($params){
		return ['template' => 'aboutGoatTeam.html'];
	}

	public static function aboutPointGame($params){
		return ['template' => 'aboutPointGame.html'];
	}

	public static function tipsContent($params){
		return ['template' => 'tipsinfo.html'];
	}

	public static function inspoContent($params){
		return ['template' => 'inspoinfo.html'];
	}

	public static function premiumPage($params){
		return ['template' => 'premiumPage.html'];
	}
}