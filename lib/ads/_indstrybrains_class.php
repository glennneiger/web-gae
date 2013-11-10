<?php

class industrybrains{
	public function displayads($pageName,$size){
		if($this->config[$pageName][$size])
		{
			echo $this->config[$pageName][$size];
		}else{
			echo $this->config['home'][$size];
		}
	}


	public function industrybrains(){
		$this->config['home']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=HOMEPAGE300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['home']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=HOMEPAGE650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['home']['1x1']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=ARTICLEPAGELISTINGSONELINETEXTLINK&amp;num=1&amp;layt=5&amp;fmt=simp"></script>';

		$this->config['specialfeatures']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=FEATURESSECTION300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['specialfeatures']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=FEATURESARTICLES650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_specialfeatures']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=FEATURESARTICLES300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['article_specialfeatures']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=FEATURESARTICLES650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_specialfeatures']['1x1']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=FEATURESONELINETEXTLINK&amp;num=1&amp;layt=5&amp;fmt=simp"></script>';		

		$this->config['markets']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MARKETSSECTION300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['markets']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MARKETSARTICLES600X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_markets']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=MARKETSARTICLES300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['article_markets']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MARKETSARTICLES600X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_markets']['1x1']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=MARKETSONELINETEXTLINK&amp;num=1&amp;layt=5&amp;fmt=simp"></script>';


		$this->config['investing']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=PERSONALFINANCESECTION300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['investing']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=PERSONALFINANCEARTICLES650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_investing']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=PERSONALFINANCEARTICLES650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';
		$this->config['article_investing']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=PERSONALFINANCESECTION300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
			
/*		$this->config['article_investing']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=PERSONALFINANCEONELINETEXTLINK&amp;num=1&amp;layt=5&amp;fmt=simp"></script>';*/

		$this->config['article_investing']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=PERSONALFINANCEARTICLES300X250&amp;num=3&amp;layt=9&amp;fmt=simp"></script>';


        $this->config['audiovideo']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_PERSONAL_FINANCE&amp;tr=VIDEO300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
		$this->config['audiovideo']['650x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=VIDEO650X250&amp;num=5&amp;layt=12&amp;fmt=simp"></script>';

		$this->config['articlelisting']['1x1']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=ARTICLEPAGELISTINGSONELINETEXTLINK&amp;num=1&amp;layt=5&amp;fmt=simp"></script>';


		$this->config['dailyfeed']['300x250']='<script type="text/javascript" src="http://jlinks.industrybrains.com/jsct?sid=713&amp;ct=MINYANVILLE_HOMEPAGE_ROS&amp;tr=DAILYFEED300X250&amp;num=4&amp;layt=9&amp;fmt=simp"></script>';
	}
}