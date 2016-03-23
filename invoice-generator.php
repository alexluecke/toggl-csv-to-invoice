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

		$total = 0.0;

		if (file_exists($this->file)) {
			if (($fd = fopen($this->file, "r")) !== FALSE) {
				$data = fgetcsv($fd, 1000, ",");
				while (($data = fgetcsv($fd, 1000, ",")) !== FALSE) {
					$t_str = $data[11];
					list($h, $m, $s) = explode(":", $t_str);
					$total += (double)intval($h)
						+ (double)intval($m)/60.0
						+ (double)intval($s)/3600.0;
				}
				fclose($fd);
			}
		}

		return $total;
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
			echo "<table id='tasks'>";
			if (($fd = fopen($this->file, "r")) !== FALSE) {

				if ($data = fgetcsv($fd, 1000, ",") !== FALSE)
					$this->print_row_header($data);

				while (($data = fgetcsv($fd, 1000, ",")) !== FALSE)
					$this->print_row($data);

				fclose($fd);
			}
			$this->print_row_time();
			echo "</table>";
		}
	}
}
