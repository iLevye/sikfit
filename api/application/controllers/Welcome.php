<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$gunler= array(0=>"Pazar",1=>"Pazartesi",2=>"Salı",3=>"Çarşamba",4=>"Perşembe",5=>"Cuma",6=>"Cumartesi");
		//echo $gunler[date("w", strtotime("-1 days"))];
		echo date("m");
	
	}

	public function test(){
		
		$rows = $this->db->get("Dizi")->result_array();
		foreach ($rows as $dizi) {
			exec("curl -o /Library/WebServer/Documents/dizilabbot/images/".$dizi['idDizi'].".png ".$dizi['image']." -H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: en-US,en;q=0.8,sl;q=0.6,tr;q=0.4' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Referer: http://dizilab.com/' -H 'Cookie: __cfduid=db9a6826f6ed4d4979111960e7af7fffd1450773960; _ym_uid=1450773970981318099; jwplayer.volume=70; cf_clearance=4c0a59025e0c29fc9c75db2d46959135bf91b1a8-1451122150-86400; PHPSESSID=5b031bf731cd15e85d0bdc2c83e27ca7; _ym_isad=1; __utma=63484730.774101749.1450773967.1450863342.1451122152.6; __utmc=63484730; __utmz=63484730.1450773967.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ga=GA1.2.774101749.1450773967' -H 'Connection: keep-alive' -H 'Cache-Control: max-age=0' --compressed");
			log_message('info', $dizi['name'] . " downloaded.");
		}
		
	}
}
