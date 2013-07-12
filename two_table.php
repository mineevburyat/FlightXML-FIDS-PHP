<?php
class two_table
{
	public $direction;
	public $time_type;
	public $arrival;
	public $departure;
	
	
	function display()
	{
		global $arrived;
		global $departed;
		global $scheduled;
		global $enroute;
		
		
		if (DISPLAY_OPTION == '2') {
			
			$this->arrival = array_merge($arrived, $enroute);
		
			usort($this->arrival, array("two_table", "cmpAE"));
		
			$this->departure = array_merge($departed, $scheduled);
			usort($this->departure, array("two_table", "cmpDS"));
			$groups =  array($this->arrival, $this->departure);
		}
		else
		{
			$groups = array($arrived, $enroute, $departed, $scheduled);
		}
		
		echo "<table width = '100%' cellpadding = '12' bgcolor = 'yellow'>";
		
		foreach ($groups as $group) {
		
		
			$this->gen_table($group);
		
		}
		
		echo "</table>";
		
		
	}
	
	static function cmpAE($flightA, $flightE) {
		if (array_key_exists("actualarrivaltime", $flightA)) {
			if (array_key_exists("actualarrivaltime", $flightE)) {
				$a = $flightA->actualarrivaltime;
				$e = $flightE->actualarrivaltime;
			}
			else{
				$a = $flightA->actualarrivaltime;
				$e = $flightE->estimatedarrivaltime;
			}
		}
		else {
			if (array_key_exists("actualarrivaltime", $flightE)) {
				$a = $flightA->estimatedarrivaltime;
				$e = $flightE->actualarrivaltime;
			}
			
			else{
				$a = $flightA->estimatedarrivaltime;
				$e = $flightE->estimatedarrivaltime;
			}
		}
	
		
		
		if ( $a == $e) {
			return 0;
		}
		
		return ($a < $e) ? -1 : 1;
	}
	
	static function cmpDS($flightD, $flightS) {
	
		if (array_key_exists("actualdeparturetime", $flightD)) {
			if (array_key_exists("actualdeparturetime", $flightS)) {
				$d = $flightD->actualdeparturetime;
				$s = $flightS->actualdeparturetime;
			}
			else{
				$d = $flightD->actualdeparturetime;
				$s = $flightS->filed_departuretime;
			}
		}
		else {
			if (array_key_exists("actualdeparturetime", $flightS)) {
				$d = $flightD->filed_departuretime;
				$s = $flightS->actualdeparturetime;
			}
			
			else{
				$d = $flightD->filed_departuretime;
				$s = $flightS->filed_departuretime;
			}
		}
	
	
	
	
		
		
		if ( $d == $s) {
			return 0;
		}
		
		return ($d < $s) ? -1 : 1;
	}

	function gen_table($group)
	{
		global $arrived;
		global $departed;
		global $scheduled;
		global $enroute;
		global $departure;
		global $arrival;
		if ($group == $arrived || $group == $enroute || $group == $this->arrival) {
			$this->time_type = "Arrival Time";
			$this->direction = "Origin";
		}
		else{
			$this->time_type = "Departure Time";
			$this->direction = "Destination";
		}
		
	
			switch($group) {

			case $departed:
				
				$caption = "DEPARTED";
				
				$this->print_header($caption);
				
				$this->gen_departed_content($group);
				
				break;
	
			case $arrived:
				
				$caption = "ARRIVED";
				
				$this->print_header($caption);
			
				$this->gen_arrived_content($group);
			
				break;	
			case $scheduled:
			
				$caption = "SCHEDULED";
				
				$this->print_header($caption);
			
				$this->gen_scheduled_content($group);
				break;
			case $enroute:
			
				$caption = "ENROUTE";
				
				$this->print_header($caption);
				$this->gen_enroute_content($group);
				break;
			case $this->departure:
				$caption = "DEPARTURE";
				
				$this->print_header($caption);
				
				$this->gen_departure_content($group);
				break;
				
			case $this->arrival:
				$caption = "ARRIVAL";
				
				$this->print_header($caption);
				
				$this->gen_arrival_content($group);
			
				break;	
				break;
		}			
	
		
	}
	
	
	
	
	function print_header($caption) {
		
		echo "
			
			<thead>
			<tr><th align = 'center' colspan = '5'>"."<br/>".AIRPORT_FULLNAME."<br/>"."<br/>".$caption."</th></tr>";
				if ($caption == "ARRIVAL" || $caption == "DEPARTURE") {
					echo "<tr><th align = 'center'>".$this->date_time_convert(time())."</th></tr>";
				}
				echo "
				<tr>
					<th>Ident</th>
					<th>Aircraft Type</th>
					<th>".$this->direction."</th>
					<th>".$this->time_type."</th>
					<th>Status</th>
				</tr>
			<thead>";
	
	}
	
	
	function gen_arrived_content($group) 
	{	
		$status = "Arrived";
		foreach ($group as $flight) {
			
			$this->print_arrived_content($flight, $status);	

		}
	}
	
	function gen_arrival_content($group) 
	{	
	
		foreach ($group as $flight) {
			
			if (array_key_exists('actualarrivaltime', $flight)) {
				$status = "Arrived";
				$this->print_arrived_content($flight, $status);	
			}
			else {
				$status = "Enroute";
				$this->print_enroute_content($flight, $status);	
			}

		}
	}
	
	function gen_departure_content($group) 
	{	
	
		foreach ($group as $flight) {
			
			if (array_key_exists('actualdeparturetime', $flight)) {
				$status = "Departed";
				$this->print_departed_content($flight, $status);	
			}
			else {
				$status = "Scheduled";
				$this->print_scheduled_content($flight, $status);	
			}

		}
	}
	
	function gen_departed_content($group) 
	{
		$status = "Departed";
		foreach ($group as $flight) {
			
			$this->print_departed_content($flight, $status);	

		}
	}
	
	
	function gen_scheduled_content($group) 
	{
		$status = "Scheduled";
		foreach ($group as $flight) {
			
			$this->print_scheduled_content($flight, $status);	

		}
	}
	
	function gen_enroute_content($group) 
	{
		$status = "Enroute";
		foreach ($group as $flight) {
			
			$this->print_enroute_content($flight, $status);	

		}
	}
	
	function print_arrived_content($flight, $status) {
		
		
		echo "<tr>
	
			<td align = 'center'>".$flight->ident."</td>
			<td align = 'center'>".$flight->aircrafttype."</td>
			<td align = 'center'>".$flight->origin."</td>
			<td align = 'center'>".$this->time_convert($flight->actualarrivaltime)."</td>
			<td align = 'center'>".$status."</td>
	
			</tr>";
	
	}
	
	function print_departed_content($flight, $status) {
		
		
		echo "<tr>
	
			<td align = 'center'>".$flight->ident."</td>
			<td align = 'center'>".$flight->aircrafttype."</td>
			<td align = 'center'>".$flight->destination."</td>
			<td align = 'center'>".$this->time_convert($flight->actualdeparturetime)."</td>
			<td align = 'center'>".$status."</td>
	
			</tr>";
	
	}
	
		function print_scheduled_content($flight, $status) {
			
			if ($flight->filed_departuretime < time()) {
				$status = "Delayed";
			}
		
			echo "<tr>
	
				<td align = 'center'>".$flight->ident."</td>
				<td align = 'center'>".$flight->aircrafttype."</td>
				<td align = 'center'>".$flight->destination."</td>
				<td align = 'center'>".$this->time_convert($flight->filed_departuretime)."</td>
				<td align = 'center'>".$status."</td>
	
				</tr>";
	
	}
	
	function print_enroute_content($flight, $status) {
	
		if ($flight-> actualdeparturetime == '0') {
			if ($flight->filed_departuretime < time()) {
				$status = "Delayed";
			}
			else{
				$status = "Scheduled";
				}
			}
		
		echo "<tr>
	
			<td align = 'center'>".$flight->ident."</td>
			<td align = 'center'>".$flight->aircrafttype."</td>
			<td align = 'center'>".$flight->origin."</td>
			<td align = 'center'>".$this->time_convert($flight->estimatedarrivaltime)."</td>
			<td align = 'center'>".$status."</td>
	
			</tr>";
	}
	
	
	function time_convert($epoch) 
	{
		if ( $epoch >= (time()- time()%86400 + 86400) || $epoch < (time()- time()%86400)) {
			$dt = new DateTime("@$epoch");
			return ($dt->format('H:i M/d'));
		}
		$dt = new DateTime("@$epoch");
		
		
		return ($dt->format("H:i"));
	}
	
	function date_time_convert($epoch) 
	{
		$dt = new DateTime("@$epoch");
		return ($dt->format('Y-m-d H:i'));
	}
	
}
?>