<?php
	class Sidebar extends CI_Controller{

		function get(){
			$user_id = intval(1);
			$sql = $this->db->query("select Dizi.name, Dizi.idDizi as id from SidebarCollectionDizi left join Dizi on (dizi_id = idDizi) where sidebarcollection_id = (select User.sidebarcollection_id from User where User.id = $user_id);")->result_array();
			$data['list'] = $sql;

			$this->output
    ->set_content_type('application/json')
    ->set_output(json_encode($data));
		}
	}
?>