#!/usr/bin/php
<?php

global $argv;

class GoPro {
	private $ip;
	private $pt;
	private $pw;
	private $md;
	function __construct($pw, $md="Hero3+", $ip="10.5.5.9", $pt="80") {
	  $this->ip = $ip;
	  $this->pt = $pt;
	  $this->pw = $pw;
	}
	
	function run($dev, $app, $com) {
	  $return = file_get_contents("http://$this->ip:$this->pt/$dev/$app?t=$this->pw&p=%$com");	
	  print_r($return);
	}
	
	function action($act) {
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
		  case 'SHOOT':
		  	$this->run('bacpac', 'SH', '01');
		  	break;
		  case 'STOP':
		  	$this->run('bacpac', 'SH', '00');
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
		  default:
		  	print "Command '$act' not understood.\n";
		  	break;
		}
	}
}


$gopro = new GoPro('PASSWORD');

$gopro->action($argv[1]);