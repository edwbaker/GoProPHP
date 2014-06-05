#!/usr/bin/php
<?php

global $argv;

require_once('settings.php');

define('Hero3plusBlack', "HERO3+ Black Edition", true);

class GoPro {
	private $ip;
	private $pt;
	private $pw;
	private $md;
	private $ve;
	private $md_url;
	private $files;
	function __construct($pw, $md="auto", $ip="10.5.5.9", $pt="80") {
	  $this->ip = $ip;
	  $this->pt = $pt;
	  $this->pw = $pw;
	  if ($md == "auto") {
	  	$this->md = $this->getModel();
	  }
	  else {
	    $this->md = $md;
	  }
	  if ($report_new_files) {
	    $this->files = $this->files();
	  }
	  switch ($this->md) {
	  	case Hero3plusBLACK:
	  		$this->md_url = 'http://gopro.com/cameras/hd-hero3-black-edition';
	  		break;
	  }
	}
	
	function getModel() {
		$bytes = file_get_contents("http://$this->ip:$this->pt/camera/cv");
		$this->ver  = substr($bytes, 4, 12);
		$model_name = substr($bytes, 17);
		switch ($model_name) {
			case Hero3plusBlack: 
			  $this->md = Hero3plusBlack;
		}
		
		return $model_name;
	}
	
	function run($dev, $app, $com) {
	  $return = file_get_contents("http://$this->ip:$this->pt/$dev/$app?t=$this->pw&p=%$com");	
	  //print_r($return);
	}
	
	function action($act) {
		$return = array();
		$return['http://dbpedia.org/resource/Camera'] = $this->md_url;
		switch ($act){
		  case 'CMODE':
		    $this->run('bacpac', 'PW', '02');
		    print "[INFO  ] It is better to set mode specifically.\n";
		    break;
		  case 'TURNON':
		  	$this->run('bacpac', 'PW', '01');
		  	break;
		  case 'TURNOFF':
		  	$this->run('bacpac', 'PW', '00');
		  	break;
		  case 'START':
		  	if ($report_new_files) {
		  	  file_put_contents(sys_get_temp_dir().'/gopro_vid', json_encode($this->files()));
		  	}
		  	$this->run('bacpac', 'SH', '01');
		  	break;
		  case 'SHOOT':
		  	$this->run('bacpac', 'SH', '01');
		  	if ($report_new_files) {
		  	  $return['http://dbpedia.org/resource/Photograph'] = $this->newfiles();
		  	}
		  	break;
		  case 'STOP':
		  	$this->run('bacpac', 'SH', '00');
		  	if ($report_new_files) {
		  	  $this->files = json_decode(file_get_contents(sys_get_temp_dir().'/gopro_vid'), TRUE);
		  	  unlink(sys_get_temp_dir().'/gopro_vid');
		  	  $return['http://dbpedia.org/resource/Video'] = $this->newfiles();
		  	}
		  	break;
		  case 'PREVON':
		  	$this->run('camera', 'PV', '02');
		  	break;
		  case 'PREVOFF':
			$this->run('camera', 'PV', '00');
		  	break;
          case 'TLAPSE':
		  	$this->run('camera', 'CM', '03');
		  	break;
		  case 'BURST':
		  	$this->run('camera', 'CM', '02');
		  	break;
		  case 'PHOTO':
		  	$this->run('camera', 'CM', '01');
		  	break;
		  case 'VIDEO':
		  	$this->run('camera', 'CM', '00');
		  	break;
		  case 'UDOWN':
		  	$this->run('camera', 'UP', '01');
		  	break;
		  case 'UUP':
		  	$this->run('camera', 'UP', '00');
		  case '4K#12':
		  	$this->run('camera', 'VR', '02');
		  	break;
		  case '2.7K#24':
		  	$this->run('camera', 'VR', '03');
		  	break;
		  case '960#48':
		  	$this->run('camera', 'VR', '06');
		  	break;
		  case 'listfiles':
		  	$files = $this->files();
		  	print_r($files);
		  	break;
		  default:
		  	print "Command '$act' not understood.\n";
		  	break;
		}
		return $return;
	}
	
	function newfiles() {
		$newfiles = $this->files();
		$added_files = array();
		foreach ($newfiles as $newfile) {
			if (!in_array($newfile, $this->files)) {
				$added_files[] = $newfile;
			}
		}
		$this->files = $newfiles;
		return $added_files;
	}
	
	function files() {
		$ignore = array('Name', 'Size');
		$folders = array();
		$files = array();
		$files_base_url = 'http://'.$this->ip.':8080/DCIM/';
		$page = file_get_contents($files_base_url);
        $DOM = new DOMDocument();
        $DOM->loadHTML($page);
        $elements = $DOM->getElementsByTagName('a');
        for ($i = 0; $i < $elements->length; $i++) {
        	if (!in_array($elements->item($i)->textContent, $ignore)) {
        	  $folders[] = $elements->item($i)->textContent;
        	} 
        }
        
        foreach ($folders as $folder) {
        	$folder_base_url = $files_base_url . $folder . '/';
        	$page = file_get_contents($folder_base_url);
        	$DOM = new DOMDocument();
        	$DOM->loadHTML($page);
        	$elements = $DOM->getElementsByTagName('a');
        	for ($i = 0; $i < $elements->length; $i++) {
        		if (!in_array($elements->item($i)->textContent, $ignore)) {
        			$files[] = $folder_base_url . $elements->item($i)->textContent;
        		}
        	}
        }
        
		return $files;
	}
}


$gopro = new GoPro($pw, Hero3plusBlack);

print_r($gopro->action($argv[1]));
