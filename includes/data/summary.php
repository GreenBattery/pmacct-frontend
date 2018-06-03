<?php
/**
 * Data model for retriving a summary of statistics
 * @author Daniel15 <daniel at dan.cx>
 */

require_once(dirname(dirname(__FILE__)) . '/ip.lib.php');

class Data_Summary
{
	/**
	 * Get the statistics for a certain day
	 * @param	date	Minimum date
	 * @return	Array of data
	 */
	public static function day($date)
	{
		// Calculate the last second of this day
		$end_date = mktime(23, 59, 59, date('m', $date), date('d', $date), date('Y', $date));
		
		return self::summary($date, $end_date);
	}
	
	/**
	 * Get the statistics for a certain month
	 * @param	date	Minimum date, in unix epoch.
	 * @return	Array of data
	 */
	public static function month($date)
	{
        date_default_timezone_set(Config::$tz);
		// Calculate end of this month

        var_dump($date);
        $last_day = date('t', $date); //get the last day of this month from timestamp.

        var_dump("lastday: " . $last_day . " of " . date('n', $date));

        var_dump($date);
        //get the epoch in localtime?
		$end_date = mktime(23, 59, 59, date('n', $date), $last_day);
		var_dump($end_date);
		
		//$data =  self::summary($date, $end_date);

        //make the table name in _mmYY format. for inbound table
        $table_in = "inbound_" . date("mY", $date);
        $table_out = "outbound_" . date("mY", $end_date);

        $query = Database::getDB()->prepare("
			SELECT ip_src AS ip, UNIX_TIMESTAMP(stamp_inserted) AS hour, bytes AS bytes_out, ip_proto AS protocol, dst_port
			FROM $table_out
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
			ORDER BY stamp_inserted, ip_src");

        $query->execute(array(
            ':start_date' => $date,
            ':end_date' => $end_date,
        ));

        $data = array(); // prepare results array.
        $totals = array(
            'in'=>0,
            'out'=>0,
            'tcp'=> array(
                'in'=> 0,
                'out'=>0
            ),
            'udp'=> array(
                'in'=>0,
                'out'=>0
            ),
            'other'=> array(
                'in'=>0,
                'out'=>0
            )
            );


        while ($row = $query->fetch(PDO::FETCH_NAMED))
        {
            //collapse uninteresting protocols to 'other'
            if (!in_array($row['protocol'], array('tcp', 'udp', 'icmp'))  ){
                $row['protocol'] = 'other';
            }
            //var_dump($row);
            if (!array_key_exists( $row['ip'], $data)) {

                //initialise all fields for this IP
                $data[$row['ip']] = array(
                    'udp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'tcp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'icmp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'other' => array('bytes_in'=>0, 'bytes_out' => 0),
                );

            }


            //populate the values accordingly.
            $data[$row['ip']][$row['protocol']]['bytes_out'] += $row['bytes_out'];

            $totals[$row['protocol']]['out'] += $row['bytes_out'];
            $totals['out'] += $row['bytes_out'];

        }


        $query = Database::getDB()->prepare("
			SELECT ip_dst AS ip, UNIX_TIMESTAMP(stamp_inserted) AS hour, bytes AS bytes_in, ip_proto AS protocol, src_port
			FROM $table_in
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
			ORDER BY stamp_inserted, ip_dst");

        $query->execute(array(
            ':start_date' => $date,
            ':end_date' => $end_date,
        ));

        while ($row = $query->fetch(PDO::FETCH_NAMED))
        {
            //collapse uninteresting protocols to 'other'
            if (!in_array($row['protocol'], array('tcp', 'udp', 'icmp'))  ){
                $row['protocol'] = 'other';
            }
            //var_dump($row);
            if (!array_key_exists( $row['ip'], $data)) {
                //initialise all fields for this IP
                $data[$row['ip']] = array(
                    'udp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'tcp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'icmp' => array('bytes_in'=>0, 'bytes_out' => 0),
                    'other' => array('bytes_in'=>0, 'bytes_out' => 0),
                );

            }

            $data[$row['ip']][$row['protocol']]['bytes_in'] += $row['bytes_in'];

            $totals[$row['protocol']]['in'] += $row['bytes_in'];
            $totals['in'] += $row['bytes_in'];

        }


		//perform additional categorisation
        $res = array('data'=> $data, 'totals'=>$totals);

        var_dump($res);
        //return data
        return $res;
	}
	
	/**
	 * Get a summary of host traffic data for a certain time period
	 * @param	date	Start of this time perioud
	 * @param	date	End of this time period
	 * @return	Array of data
	 */
	private static function summary($start_date, $end_date)
	{
        date_default_timezone_set(Config::$tz);
        $block = IPBlock::create(Config::$localSubnet);
        $i = 0; //use this counter to avoid expanding more than 256 IP addresses.
        $addresses = array(); //hold list of IPs. we set hard limit of 256 addresses.
        foreach ($block as $ip){
            $addresses[] = (string) $ip;
            $i++;

            if ($i >=255){
                break;
            }
        }

        //format the list of IP addresses in our /24 subnet for use in mysql IN clause.
        $in = "(" . implode(",",$addresses) . ")";

        //make the table name in _mmYY format. for inbound table
        $table_in = "inbound_" . date("mY", $start_date);
        $table_out = "outbound_" . date("mY", $start_date);


        //get a database connection via PDO object
		$db = Database::getDB();

		//sql for fetching inbound/download stats.
		$sql = "
			SELECT ip_dst AS ip, SUM(bytes) AS bytes_in
			FROM   $table_in 
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME($start_date) AND FROM_UNIXTIME($end_date)
			GROUP BY ip
			ORDER BY bytes_in DESC";
			
		$results = $db->query($sql);


		//syslog(LOG_NOTICE, "stat: summary query returned: " . $results . " items");
		
		$data = array();
		$totals = array(
			'bytes_out' => 0,
			'bytes_in' => 0,
			'bytes_total' => 0,
		);
		
		foreach ($results as $row)
		{
		    $data[$row['ip']]['bytes_in'] = (int)$row['bytes_in'];
		    $totals['bytes_in'] += (int) $row['bytes_in'];


		}

		//sql for fetching outbound/upload stats.
        $sql = "
			SELECT ip_src AS ip, SUM(bytes) AS bytes_out
			FROM   $table_out
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME($start_date) AND FROM_UNIXTIME($end_date)
			GROUP BY ip";

        $results = $db->query($sql);

        //process returned outbound stats.
        foreach ($results as $row)
        {
            //var_dump($row);
            $data[$row['ip']]['bytes_out'] = (int) $row['bytes_out'];
            $totals['bytes_out'] += (int) $row['bytes_out'];


        }

		$totals['bytes_total'] = $totals['bytes_in'] + $totals['bytes_out'];
		return array(
			'data' => $data,
			'totals' => $totals
		);
	}
	
	/**
	 * Get the statistics for a certain month, grouped by day and host
	 * @param	date	Minimum date
	 * @return	Array of data
	 */
	public static function month_by_day($start_date)
	{
		// Calculate end of this month
		$end_date = mktime(23, 59, 59, date('m', $start_date) + 1, 0, date('Y', $start_date));
		
		$query = Database::getDB()->prepare('
			SELECT ip, UNIX_TIMESTAMP(date) AS date, SUM(bytes_out) bytes_out, SUM(bytes_in) bytes_in
			FROM ' . Config::$database['prefix'] . 'combined
			WHERE date BETWEEN :start_date AND :end_date
			GROUP BY ip, DAY(date)
			ORDER BY date, ip');
			
		$query->execute(array(
			'start_date' => Database::date($start_date),
			'end_date' => Database::date($end_date),
		));
		
		// Start with an empty array for all the days of the month
		$day_base = date('Y-m-', $start_date);
		$days = array();
		for ($i = 1, $count = date('t', $start_date); $i <= $count; $i++)
		{
			$days[$day_base . str_pad($i, 2, '0', STR_PAD_LEFT)] = 0;
		}

		$data = array();
		while ($row = $query->fetchObject())
		{
			// Check if this IP is on the list of IPs that should be shown
			if (!empty(Config::$include_ips) && !in_array($row->ip, Config::$include_ips))
				continue;
				
			// Does this host have a data entry yet?
			if (!isset($data[$row->ip]))
				$data[$row->ip] = $days;
			
			$row->bytes_total = $row->bytes_in + $row->bytes_out;
			$data[$row->ip][date('Y-m-d', $row->date)] =  $row->bytes_total;
		}
		
		return $data;
	}
}
?>