<?php
error_reporting(-1);
ini_set('display_errors', 1);


require 'simplehtmldom_1_5/simple_html_dom.php';
require 'db/MysqliDb.php';

$curl_params = "-H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: en-US,en;q=0.8,sl;q=0.6,tr;q=0.4' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Referer: http://dizilab.com/' -H 'Cookie: __cfduid=db9a6826f6ed4d4979111960e7af7fffd1450773960; _ym_uid=1450773970981318099; __utma=63484730.774101749.1450773967.1450793513.1450800637.4; __utmc=63484730; __utmz=63484730.1450773967.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _ga=GA1.2.774101749.1450773967; jwplayer.volume=70; cf_clearance=3ef698bd766334267b2efb2cb6d776eee91a8a80-1450863340-86400' -H 'Connection: keep-alive' --compressed";


function diziyi_cek($url, $dizi_id){
	global $curl_params, $db;

	exec("curl '".$url."' " . $curl_params, $output);

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
				$array['files'] = bolum_detay_cek($array['dizilab_link']);
				$array['sezon_no'] = $sezon_no;
				$array['dizi_id'] = $dizi_id;
				//$db->insert('Bolum', $array);
				$sezonlar[$sezon_no][] = $array;
			}
		}
		$sezon_no += 1;
	}

	return $sezonlar;
}


function dizi_listesi_cek(){
	global $curl_params, $db;
	$url = "http://dizilab.com/arsiv?sayfa=1&limit=5&tur=&orderby=&order=&yil=&dizi_adi=&ulke=";
	exec("curl '".$url."' " . $curl_params, $output);

	$html = str_get_html(implode($output, ''));

	$diziler = $html->find('.tv-series-single');
	$id = 1;
	foreach ($diziler as $d) {
		$array['idDizi'] = $id;
		$array['image'] = $d->find('a', 0)->find('img')[0]->src;
		$array['name'] = $d->find('a.title')[0]->innertext;
		$array['name'] = explode('span>', $array['name'])[1];

		$array['link'] = $d->find('a.title')[0]->href;
		$array['imdb_rank'] = $d->find('.rank-text', 0)->innertext;
		$array['yapim_yili'] = $d->find("ul li", 0)->find('span', 1)->innertext;
		$array['sezon_bolum'] = $d->find("ul li", 1)->find('span', 1)->innertext;
		$array['tur'] = $d->find("ul li", 2)->find('span', 1)->innertext;
		$array['ulke'] = $d->find("ul li", 3)->find('span', 1)->innertext;
		$array['short_description'] = $d->find("p.series-summery", 0)->innertext;
		//$db->insert('Dizi', $array);
		$array['sezonlar'] = diziyi_cek($array['link'], $id);
		$series[] = $array;
		$id++;
	}
	return $series;
}

function bolum_detay_cek($url){
	global $curl_params;
	exec("curl '".$url."' " . $curl_params, $output);

	//$html = str_get_html(implode($output, ''));
	$explode = explode('sources: [', implode($output, ''));
	$explode = explode(']', $explode[1]);
	$files = $explode[0];
	$files = str_replace(" ", "", $files);
	return $files;
}

//bolum_detay_cek("http://dizilab.com/planet-earth/sezon-1/bolum-2");

//print_r(diziyi_cek('http://dizilab.com/planet-earth'));

//print_r($series);

$data = dizi_listesi_cek();


?>