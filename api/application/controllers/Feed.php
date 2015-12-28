<?php
	class Feed extends CI_Controller{

		function get(){
			$old_day = 0;
			if($this->input->get("old_day") != false){
				$old_day = $this->input->get("old_day");
			}
			$t2 = 5 + $old_day;
			$t1 = 0 + $old_day;

			$this->db->select("Bolum.*, Dizi.name as series_name");
			$this->db->where("relase_date > DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'), INTERVAL $t2 DAY) and relase_date <= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'), INTERVAL $t1 DAY)");
			$this->db->join("Dizi", "Dizi.idDizi = Bolum.dizi_id", "left");
			$this->db->order_by("relase_date desc");
			$rows = $this->db->get('Bolum')->result_array();
			//echo $this->db->last_query();

			$gunler= array(0=>"Pazar",1=>"Pazartesi",2=>"Salı",3=>"Çarşamba",4=>"Perşembe",5=>"Cuma",6=>"Cumartesi");
			$aylar = array("01" => "Ocak", "02" => "Şubat", "03" => "Mart", "04" => "Nisan", "05" => "Mayıs", "06" => "Haziran", "07"=> "Temmuz", "08" => "Ağustos", "09" => "Eylül", "10" => "Ekim", "11" => "Kasım", "12" => "Aralık");

			for ($i=0; $i <= 4 ; $i++) { 
				$days[date('Ymd', strtotime("-".($i + $old_day)." days"))]['title'] = $gunler[date("w", strtotime("-".($i + $old_day)." days"))];
				$days[date('Ymd', strtotime("-".($i + $old_day)." days"))]['tr_date'] = date("d", strtotime("-".($i + $old_day)." days")) . " " . $aylar[date("m", strtotime("-".($i + $old_day)." days"))];
			}
			
			$data['keys'] = array(
					date('Ymd', strtotime("-".$t1." days")),
					date('Ymd', strtotime("-".($t1 + 1)." days")),
					date('Ymd', strtotime("-".($t1 + 2)." days")),
					date('Ymd', strtotime("-".($t1 + 3)." days")),
					date('Ymd', strtotime("-".($t1 + 4)." days")));


			foreach ($rows as $r) {
				$days[str_replace("-", "", $r['relase_date'])]['episodes'][] = $r;
			}
			$data['list'] = $days;

			$this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
			
		}
	}
?>