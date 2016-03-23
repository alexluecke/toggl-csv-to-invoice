<?php

class InvoiceGenerator {

	/*
	array(14) {
	[0] => "User"
	[1] => "Email"
	[2] => "Client"
	[3] => "Project"
	[4] => "Task"
	[5] => "Description"
	[6] => "Billable"
	[7] => "Start date"
	[8] => "Start time"
	[9] => "End date"
	[10] => "End time"
	[11] => "Duration"
	[12] => "Tags"
	[13] => "Amount ()" }
	*/

	private $file;
	private $rate;

	public function __construct($args) {
		extract($args);
		$this->file = $file;
		$this->rate = $rate;
	}

	public function get_total_hours() {
		$total = array();
		$total['hours'] = 0;
		$total['minutes'] = 0;
		$total['seconds'] = 0;
		if (file_exists($this->file)) {
			$handle = fopen($this->file, "r");
			if (($handle = fopen($this->file, "r")) !== FALSE) {
				$data = fgetcsv($handle, 1000, ",");
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$hour_string = $data[11];
					list($hour, $min, $sec) = explode(":", $hour_string);
					$total['hours']   += intval($hour);
					$total['minutes'] += intval($min);
					$total['seconds']     += intval($sec);
				}
				fclose($handle);
			}
		}
		$time = ((double)$total['hours']);
		$time += ((double)$total['minutes'])/60.0;
		$time += ((double)$total['seconds'])/3600.0;
		return $time;
	}

	public function print_row($row=array()) {
		echo "<tr>\n";
		echo "<td class='desc'>";
		echo (trim($row[5]) == '') ? 'No Description' : $row[5];
		echo	"</td>\n";
		echo "<td class='project'>" . $row[3] . "&nbsp;&nbsp;</td>\n";
		echo "<td class='date'>" . $row[7] . "</td>\n";
		echo "<td class='hours'>"  . $row[11] . "</td>\n";
		echo "</tr>\n";
	}

	public function print_row_header($row=array()) {
		echo "<thead>\n";
		echo "<tr>\n";
		echo "<td class='desc'>Description</td>\n";
		echo "<td class='project'>Project</td>\n";
		echo "<td class='date'>Date</td>\n";
		echo "<td class='duration'>Hours</td>\n";
		echo "</tr>\n";
		echo "</thead>\n";
	}

	public function print_row_time() {
		$total_hours = round($this->get_total_hours(), 2);
		echo "<tfoot>\n";
		echo "<td class='desc'>&nbsp;</td>\n";
		echo "<td class='project'>&nbsp;</td>\n";
		echo "<td class='date total'>Total Hours:&nbsp;</td>\n";
		echo "<td class='duration'>" . $total_hours . " Hours</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='desc'>&nbsp;</td>\n";
		echo "<td class='project'>&nbsp;</td>\n";
		echo "<td class='date total'>Rate:&nbsp;</td>\n";
		echo "<td class='duration'>$" . $this->rate . ".00/hr</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td class='desc'>&nbsp;</td>\n";
		echo "<td class='project'>&nbsp;</td>\n";
		echo "<td class='date total'>Total Charge:&nbsp;</td>\n";
		echo "<td class='duration'>$";
		printf('%01.2f', $this->rate*$total_hours);
		echo "</td>\n";
		echo "</tr>\n";
		echo "</tfoot>\n";
	}

	public function print_invoice() {
		if (file_exists($this->file)) {
			$handle = fopen($this->file, "r");
			echo "<table id='tasks'>";
			if (($handle = fopen($this->file, "r")) !== FALSE) {

				if ($data = fgetcsv($handle, 1000, ",") !== FALSE)
					$this->print_row_header($data);

				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
					$this->print_row($data);

				fclose($handle);
			}
			$this->print_row_time();
			echo "</table>";
		}
	}
}
