<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bot extends CI_Controller {

	private $curl_params = "-H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: en-US,en;q=0.8,sl;q=0.6,tr;q=0.4' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Referer: http://dizilab.com/' -H 'Cookie: __cfduid=db9a6826f6ed4d4979111960e7af7fffd1450773960; _ym_uid=1450773970981318099; jwplayer.volume=70; cf_clearance=4c0a59025e0c29fc9c75db2d46959135bf91b1a8-1451122150-86400; PHPSESSID=5b031bf731cd15e85d0bdc2c83e27ca7; _ym_isad=1; __utma=63484730.774101749.1450773967.1450863342.1451122152.6; __utmc=63484730; __utmz=63484730.1450773967.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ga=GA1.2.774101749.1450773967' -H 'Connection: keep-alive' -H 'Cache-Control: max-age=0' --compressed";

	public function index()
	{
		set_time_limit(0);
		$this->load->helper("baskabisey");

		baskabisey_yap();

		echo "hede";
				//bolum_detay_cek("http://dizilab.com/planet-earth/sezon-1/bolum-2");

		//print_r(diziyi_cek('http://dizilab.com/planet-earth'));

		//print_r($series);

		$this->load->helper("simplehtmldom");

		

		$data = $this->dizi_listesi_cek();

		print_r($data);
	}


	function var_mi($link, $name){
		$this->db->where("dizilab_link", $link);
		$rows = $this->db->get("Dizi")->result_array();
		if(@$rows[0]['idDizi'] != ""){
			log_message("info", $name . " already exist. skipped.");
			return true;
		}else{
			return false;
		}

	}

	function tarih_duzelt(){

	}


	function diziyi_cek($url, $dizi_id, $dizi_name){
		exec("curl '".$url."' " . $this->curl_params, $output);

		$output = implode($output, '
			');

		$html = str_get_html($output);

		$seasons = $html->find(".episode-tab ul");

		$sezon_no = 1;
		foreach ($seasons as $season) {
			foreach($season->find('li') as $episode){
				if(@$episode->find(".episode-name")[0] != ""){
					$array['dizilab_link'] = $episode->find(".episode-name")[0]->href;
					$array['episode_name'] = $episode->find(".episode-name")[0]->innertext;
					$array['relase_date'] = $episode->find(".date")[0]->innertext;
					log_message('info', $dizi_name . " - " . $array['episode_name'] . " loading...");
					$array['files'] = $this->bolum_detay_cek($array['dizilab_link']);
					$array['sezon_no'] = $sezon_no;
					$array['dizi_id'] = $dizi_id;
					$this->db->insert('Bolum', $array);
					$sezonlar[$sezon_no][] = $array;
				}
			}
			$sezon_no += 1;
		}

		return $sezonlar;
	}

	function get_insert($diziler){
		foreach ($diziler as $d) {
			unset($array['idDizi']);
			$array['image'] = $d->find('a', 0)->find('img')[0]->src;
			$array['name'] = $d->find('a.title')[0]->innertext;
			$array['name'] = explode('span>', $array['name'])[1];
			$array['dizilab_link'] = $d->find('a.title')[0]->href;
			$array['imdb_rank'] = $d->find('.rank-text', 0)->innertext;
			$array['yapim_yili'] = $d->find("ul li", 0)->find('span', 1)->innertext;
			$array['sezon_bolum'] = $d->find("ul li", 1)->find('span', 1)->innertext;
			$array['tur'] = $d->find("ul li", 2)->find('span', 1)->innertext;
			$array['ulke'] = $d->find("ul li", 3)->find('span', 1)->innertext;
			$array['short_description'] = $d->find("p.series-summery", 0)->innertext;
			
			if(!$this->var_mi($array['dizilab_link'], $array['name'])){
				$this->db->insert('Dizi', $array);
				$array['idDizi'] = $this->db->insert_id();

				log_message('info', $array['name'] . " loading...");
				$this->diziyi_cek($array['dizilab_link'], $array['idDizi'], $array['name']);	
				log_message('info', $array['name'] . " finished...");
			}
			
			$series[] = $array;
		}
		return $series;
	}

	function update_relase_date(){
		$this->db->where("relase_date like '%=>%'");
		$bolumler = $this->db->get("Bolum")->result_array();

		foreach ($bolumler as $b) {

			//exec("curl '".$d['dizilab_link']."' " . $this->curl_params, $output);

			// $output = implode($output, '
			// 	');

			// $html = str_get_html($output);

			// $seasons = $html->find(".episode-tab ul");

			$aylar = array("Ocak" 	=> "01",
							"Şubat" => "02",
							"Mart" 	=> "03",
							"Nisan"	=> "04",
							"Mayıs"	=> "05",
							"Haziran" => "06",
							"Temmuz"	=> "07",
							"Ağustos"	=> "08",
							"Eylül"		=> "09",
							"Ekim"		=> "10",
							"Kasım"		=> "11",
							"Aralık"	=> "12"
				);
			$update['relase_date'] = str_replace(" ", "", $b['relase_date']);
			$update['relase_date'] = str_replace("	", "", $update['relase_date']);
			$update['relase_date'] = str_replace("	", "", $update['relase_date']);

			$yil = substr($update['relase_date'], -4);
			$gun = substr($update['relase_date'], 0, 2);
			
			$chars = "aAbBcCdDeEfFgGhHijJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZçÇşŞiİıIöÖğĞüÜ";
			$pattern = "/[^".preg_quote($chars, "/")."]/";
			$ay = preg_replace($pattern, "", $update['relase_date']);
			$ay = @$aylar[$ay];

			$update['relase_date'] = $yil . "-" . $ay . "-" . $gun;
			$update['relase_date'] = $b['relase_date'] . " => " . $update['relase_date'] . "<br>";


			$e = explode("=> ", $b['relase_date']);
			$e = str_replace("<br>", "", $e[1]);
			$update['relase_date'] = $e;
			echo $update['relase_date'] . "<br>";
			$this->db->where("idBolum", $b['idBolum']);
			$this->db->update("Bolum", $update);

			// foreach ($seasons as $season) {
			// 	foreach($season->find('li') as $episode){
			// 		if(@$episode->find(".episode-name")[0] != ""){
			// 			$update['relase_date'] = $episode->find(".date")[0]->innertext;
			// 			$this->db->where('dizilab_link', $d['id']);
			// 			$this->db->update('Bolum', $update);
			// 		}
			// 	}
			// }
			// log_message('info', $array['name'] . " finished...");			
			// $series[] = $array;
		}
		return $series;
	}

	function dizi_listesi_cek(){
		$url = "http://dizilab.com/arsiv?sayfa=4&limit=1000&tur=&orderby=&order=&yil=&dizi_adi=&ulke=";
		exec("curl '".$url."' " . $this->curl_params, $output);

		$html = str_get_html(implode($output, ''));

		$diziler = $html->find('.tv-series-single');
		$this->get_insert($diziler);
	}

	function bolum_detay_cek($url){
		exec("curl '".$url."' " . $this->curl_params, $output);

		//$html = str_get_html(implode($output, ''));
		$explode = explode('sources: [', implode($output, ''));
		$explode = explode(']', $explode[1]);
		$files = $explode[0];
		$files = str_replace(" ", "", $files);
		return $files;
	}
}
