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
        date_default_timezone_set(Config::$tz);
		// Calculate the last second of this day
		$start_date = (int) $date;

		//assuming start date is midnight exactly just add 86399 seconds.
		$end_date = (int) $date + 86399;

        //make the table name in _mmYY format. for inbound table
        $table_in = "inbound_" . date("mY", $start_date);
        $table_out = "outbound_" . date("mY", $start_date);


        $data = array();
        $totals = array(
            'bytes_out' => 0,
            'bytes_in' => 0,
            'bytes_total' => 0,
        );

        //handle inbound stats.
        //if IP is empty, we should redirect to day view.
		$query = "";
        if ($ip === "") {

        }else {

            //inbound stats for specific IP
            $query = Database::getDB()->prepare('
			SELECT ip_dst as ip, stamp_inserted as hour, SUM(bytes) as bytes_in, ip_proto as protocol
			FROM ' . $table_in. '
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
				AND ip_dst = :ip GROUP BY hour, protocol
			ORDER BY stamp_inserted ASC');

            $query->execute(array(
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':ip' => $ip
            ));

            while ($row = $query->fetch(PDO::FETCH_ASSOC))
            {

                //var_dump($row);

                $h = (string) explode(" ", $row['hour'])[1];

                //var_dump($row);
                if (!array_key_exists($h, $data)) {
                    $data[$h] = array(); // each hour will be an array of bytes in and out.
                    $data[$h]['bytes_in'] = (int) $row['bytes_in'];
                    $data[$h]['bytes_out'] = 0;
                    $data[$h]['bytes_total'] = 0; //init totals tally for this slot.
                }else {
                    $data[$h]['bytes_in'] += (int) $row['bytes_in'];
                }



                $totals['bytes_in'] += $row['bytes_in'];

                $data[$h]['bytes_total'] +=  (int) $row['bytes_in']; //add inbound bytes to totals for this hour.

                $totals['bytes_total'] += $row['bytes_in'];
            }

            //now outbound for specific IP
            $query = Database::getDB()->prepare('
				SELECT ip_src as ip, stamp_inserted as hour, SUM(bytes) as bytes_out
				FROM ' . $table_out . '
				WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
					AND ip_src = :ip GROUP BY hour
				ORDER BY stamp_inserted ASC');

            $query->execute(array(
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':ip' => $ip
            ));

            while ($row = $query->fetch(PDO::FETCH_ASSOC))
            {

                $h = (string) explode(" ", $row['hour'])[1];

                //var_dump($row);
                if (!array_key_exists($h, $data)) {
                    $data[$h] = array(); // each hour will be an array of bytes in and out.
                    $data[$h]['bytes_out'] = (int) $row['bytes_out'];
                    $data[$h]['bytes_in']= 0;
                    $data[$h]['bytes_total'] = 0;
                }else {
                    $data[$h]['bytes_out'] += (int) $row['bytes_out'];
                }



                $totals['bytes_out'] += $row['bytes_out'];

                $data[$h]['bytes_total'] +=  (int) $row['bytes_out']; //add inbound bytes to totals for this hour.

                $totals['bytes_total'] += $row['bytes_out'];
            }
		}

		return array(
			'data' => $data,
			'totals' => $totals
		);
	}
}
?>
