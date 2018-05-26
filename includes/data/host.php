<?php
/**
 * Data model for retrieving statistics for a particular host
 * @author Daniel15 <daniel at dan.cx>
 */
class Data_Host
{
	/**
	 * Get the statistics for a certain day
	 * @param	string	IP address of host
	 * @param	date	Minimum date
	 * @return	Array of data
	 */
	public static function day($ip, $date)
	{
	    //var_dump($ip);
	    var_dump("start: " . $date);
		// Calculate the last second of this day
		$start_date = $date;

		//assuming start date is midnight exactly just add 86399 seconds.
		$end_date = $date + 86399;

		var_dump($end_date);

        //make the table name in _mmYY format. for inbound table
        $table_in = "inbound_" . gmdate("mY", $start_date);
        $table_out = "outbound_" . gmdate("mY", $start_date);

		$query = Database::getDB()->prepare('
			SELECT ip_dst as ip, stamp_inserted as hour, bytes as bytes_in
			FROM ' . $table_in. '
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
				AND ip_dst = :ip
			ORDER BY date DESC');
			
		$query->execute(array(
			'start_date' => $start_date,
			'end_date' => $end_date,
			'ip' => $ip
		));
		
		$data = array();
		$totals = array(
			'bytes_out' => 0,
			'bytes_in' => 0,
			'bytes_total' => 0,
		);
		
		while ($row = $query->fetch())
		{

		    var_dump($row);
			$row->bytes_total = $row->bytes_in + $row->bytes_out;
			$data[] = $row;
			
			$totals->bytes_in += $row->bytes_in;
			$totals->bytes_out += $row->bytes_out;
			$totals->bytes_total += $row->bytes_total;
		}
		
		return (object)array(
			'data' => $data,
			'totals' => $totals
		);
	}
}
?>