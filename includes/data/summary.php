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

        //var_dump($date);

        //right now
        $ctime = mktime(); //current timesamp for use in main_summary.
        $cmonth = date("mY", $date); //get the MMYYYY to use as the duration key in main_summary table.

        $last_day = date('t', $date); //get the last day of this month from timestamp.

        //get the epoch in localtime?
		$end_date = mktime(23, 59, 59, date('n', $date), $last_day);

		//retrieve from summary tables to help speed things up.
		$sq = Database::getDB()->prepare("
          SELECT 
            * 
          FROM 
            main_summary 
          WHERE 
            duration_type = 'month' and 
            duration = :duration
          ");

		$sq->execute([':duration' => $cmonth]);

		$prev_stats = $sq->fetchAll(PDO::FETCH_ASSOC);

        if($prev_stats === false) {

            var_dump($sq->errorInfo());
        }

		//if $res is not empty, then we have some summaries and we need to only compute raw data since last summary.
        $last_summary = null;
        if (count($prev_stats) > 0) {
            $last_summary = $prev_stats[0]['stamp_inserted']; //this reflects last time stats were cached.

            //calculate stats only from this date onwards.
            $date = strtotime($last_summary);

        }

        //if the summary date is later than last day of the month, we don't need any computation.
        if ($date >= $end_date) {
            $date = $end_date; //make them equal and query should produce no data.
        }

		//we need to fill in the blanks from the raw data.
		//$data =  self::summary($date, $end_date);

        //make the table name in _mmYY format. for inbound table
        $table_in = "inbound_" . $cmonth;
        $table_out = "outbound_" . $cmonth;

        $query = Database::getDB()->prepare("
			SELECT ip_src AS ip, UNIX_TIMESTAMP(stamp_inserted) AS hour, bytes AS bytes_out, ip_proto AS protocol, dst_port
			FROM $table_out
			WHERE stamp_inserted BETWEEN FROM_UNIXTIME(:start_date) AND FROM_UNIXTIME(:end_date)
			ORDER BY stamp_inserted, ip_src");

        $query->execute(array(
            ':start_date' => $date,
            ':end_date' => $end_date,
        ));

        //initialise the totals array.
        $totals = array(
            'in'=>0,
            'out'=>0
        );

        $data = []; // prepare results array.
        //prefill the data and totals arrays with previously cached stats.
        foreach ($prev_stats as $pstat) {
            $data[$pstat['ip']]['bytes_in'] = $pstat['bytes_in'];
            $data[$pstat['ip']]['bytes_out'] = $pstat['bytes_out'];
            $data[$pstat['ip']]['total'] = $pstat['bytes_in'] + $pstat['bytes_out'];

            $totals['in'] += $pstat['bytes_in'];
            $totals['out'] += $pstat['bytes_out'];
        }

        $month_data = [];//we'll compute the data for main_summary table here too.



        while ($row = $query->fetch(PDO::FETCH_NAMED))
        {
            //collapse uninteresting protocols to 'other'
            if (!in_array($row['protocol'], array('tcp', 'udp', 'icmp'))  ){
                $row['protocol'] = 'other';
            }

            if (!array_key_exists( $row['ip'], $data)) {

                //initialise all fields for this IP
                $data[$row['ip']] = array(
                    'bytes_in' => 0,
                    'bytes_out' => 0,
                    'total' =>0
                );

            }

            if (!array_key_exists($row['ip'], $month_data)) { //init month data too if necessary.
                $month_data[$row['ip']] = [
                    'duration_type' => 'month',
                    'duration' => $cmonth,
                    'bytes_in' => 0,
                    'bytes_out' => 0,
                    'stamp_inserted' => $ctime
                ];
            }


            //populate the values accordingly.
            $data[$row['ip']]['bytes_out'] += $row['bytes_out'];
            $data[$row['ip']]['total'] += $row['bytes_out'];

            //update month data too
            $month_data[$row['ip']]['bytes_out'] += $row['bytes_out'];

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


        //process inbound stats.
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
                    'bytes_in' => 0,
                    'bytes_out' => 0,
                    'total' =>0
                );

            }

            if (!array_key_exists($row['ip'], $month_data)) { //init month data too if necessary.
                $month_data[$row['ip']] = [
                    'duration_type' => 'month',
                    'duration' => $cmonth,
                    'bytes_in' => 0,
                    'bytes_out' => 0,
                    'stamp_inserted' => $ctime
                ];
            }

            $data[$row['ip']]['bytes_in'] += $row['bytes_in'];
            $data[$row['ip']]['total'] += $row['bytes_in'];

            $month_data[$row['ip']]['bytes_in'] += $row['bytes_in'];

            $totals['in'] += $row['bytes_in'];

        }


        //stuff this data into the main_summary table to speed up future lookups for month stats.

        foreach($month_data as $ip=>$stats) {
            //if this stat exists in the table, then we need to update by adding to current data. else, insert.
            $sq1 = Database::getDB()->prepare("
                select * 
                from 
                  main_summary 
                where 
                  ip=:ip_addr and 
                  duration_type= 'month' and 
                  duration = :duration"
            );

            $sq1->execute([
                ":ip_addr" => $ip,
                ':duration' => $cmonth
            ]);

            //the query above should yield only one result.
            $res = $sq1->fetchAll(PDO::FETCH_NAMED)[0] ?? [];

            if (count($res) > 0 ) { //value exists, update it.
                $bytes_in = $res['bytes_in'] + $stats['bytes_in'];
                $bytes_out = $res['bytes_out'] + $stats['bytes_out'];

                $sq3 = Database::getDB()->prepare("
                    UPDATE main_summary
                    set
                      bytes_in = :bytes_in,
                      bytes_out = :bytes_out,
                      stamp_inserted = FROM_UNIXTIME(:stamp_inserted)
                    WHERE 
                      id = :id
                ");

                $res3 = $sq3->execute([
                    ':bytes_in' => $bytes_in,
                    ':bytes_out' => $bytes_out,
                    'stamp_inserted' => $ctime,
                    ':id' => $res['id']
                ]);

                //check result of query.
                if (!$res3) {
                    //write a message to syslog.
                    syslog(LOG_NOTICE,"failed to UPDATE SUMMARY 'month' data for $ip, dur: $cmonth ");
                }



            } else {
                //insert value.
                $sq2 = Database::getDB()->prepare("
                    INSERT into main_summary 
                    values (
                      null,
                      :ip_addr,
                      'month',
                      :duration,
                      :bytes_in,
                      :bytes_out,
                      FROM_UNIXTIME(:stamp_inserted)
                    )
                ");

                $res2 = $sq2->execute([
                    ':ip_addr'=> $ip,
                    ':duration' => $cmonth,
                    ':bytes_in' => $stats['bytes_in'],
                    ':bytes_out'=> $stats['bytes_out'],
                    ':stamp_inserted' => $ctime
                ]);

                //check result of query.
                if (!$res2) {
                    //write a message to syslog.
                    syslog(LOG_NOTICE,"failed to insert SUMMARY 'month' data for $ip, dur: $cmonth ");
                }
            }
        }

        /*
            * now we must make sure to update all the records from the last stat to have the current timestamp
            * in the stamp_inserted field
            *
            */

        $last_summary = $last_summary ?? date("Y-m-d h:i:s", $ctime); //ensure there's a value for last_summary.

        //first run will be a wasted cycle since we'll just set summary to the same value current.
        $sq4 = Database::getDB()->prepare("
                UPDATE 
                  main_summary
                SET 
                  stamp_inserted = FROM_UNIXTIME(:current_time)
                WHERE
                  duration_type = 'month' AND
                  stamp_inserted = :last_summary_time     
            ");

        $res4 = $sq4->execute([':current_time' => $ctime, ':last_summary_time' => $last_summary]);

        if($res4 === false) {
            //query failed.
            var_dump("failed to update cache timestamps");
            syslog(LOG_NOTICE, "stats:: failed to update main_summary cache timestamps");
        }

		//perform additional categorisation
        $res = array('data'=> $data, 'totals'=>$totals);

        //var_dump($hostnames);
        //return data
        $res['hostnames'] = Data_Summary::read_leases();
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
            $data[$row['ip']]['bytes_in'] = 0; //create it.
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
            $data[$row['ip']]['bytes_out'] = 0; //create it.
            $data[$row['ip']]['bytes_out'] = (int) $row['bytes_out'];
            $totals['bytes_out'] += (int) $row['bytes_out'];


        }

		$totals['bytes_total'] = $totals['bytes_in'] + $totals['bytes_out'];
		return array(
			'data' => $data,
			'totals' => $totals,
            'hostnames'=> Data_Summary::read_leases()
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
		$data['hostnames'] = Data_Summary::read_leases();
		return $data;
	}


	/** read dhcp lease info from dnsmasq default location
	 *
     * @param none
     * @return array of dhcp hostnames keyed by IP address (IP => hostname)
     *
     * keep in mind that the hostname is not guaranteed to exist in dhcp. it is an * if no hostname.
	 */
	private static function read_leases() {
        $fn = '/var/lib/misc/dnsmasq.leases';
        $fh = fopen($fn, 'r');
        $contents = fread($fh, filesize($fn));
        $lines = explode("\n", $contents);
        $contents = null;
        fclose($fh);

        $hostnames = array();

        foreach ($lines as $l) {

            if (strlen(trim($l))== 0) {
                continue; //skip empty lines.
            }
            $a = strpos($l, " ");
            if ($a >= 0) {
                $b = strpos($l, " ", $a+1);
                if ($b >=0 ) {
                    //now we're at ip
                    $c = strpos($l, " ", $b+1); //end of IP

                    $ip = trim(substr($l, $b, $c - $b));


                    $d = strpos($l, " ", $c+1);

                    $hn = substr($l, $c, $d - $c);

                    $hostnames[$ip] = trim($hn);
                }else {
                    break; //if space not found then not valid content
                }
            }else {
                break; //not valid content
            }
        }
        return $hostnames;
    }
}
?>