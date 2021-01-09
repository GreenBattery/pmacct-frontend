# pmacct frontend

Quick statistics frontend for [pmacct](http://www.pmacct.net/). 

# Usage

To use this, you will need Mysql and pmacct. Pmacct collects the relevant statistics
using the pcap module and stores them in mysql in a bespoke fashion as specified in the
example configuration in the docs directory. The database schema required is in the *docs/schema.sql* file and 
the required pmacct.conf file is in *docs/pmacct.conf*.

The *includes/config.php* file is where you specify the database access information in PHP syntax.

If pmacct is configured as per above and database is similarly created, then each month, the statistics are collected
into tables in the format inbound_MMYY and outbound_MMYY for inbound and outbound traffic.

You should then point your web server's Document Root to the `www`
folder. The other files that make up the project aren't intended to be 
visible from the web.

# summary cache
A cron job needs to be configured twice per hour to calculate the monthly stats like so:
`30,59 * * * * /var/www/html/batch/stats.month.calc.php --current`

# Firewall Interface Binary
To interact with firewall rules, a binary needs to be created that has the necessary
 permissions to extract firewall configuration details.

This binary is located in the bin directory as `fw.c`. You can compile it into the required binary with:
`gcc -o fw fw.c`

You also need to make it owned by root, and it's group be your web server user
with 755 permissions. For webserver group called www-data, you can use:

`sudo chown root.www-data ./fw`

`sudo chmod 755 ./fw`

Now we need to grant it setuid root capabilities, otherwise, apache cannot execute it with the right privileges.

`sudo chown u+s ./fw`


You can typically setup a crontab by executing
`sudo crontab -e`

The run at the 59th minute is important to make sure that the most recent stats are calculated before a new month.

## Requirements
* Php 7.2
* Apache 2.4
* Mysql 5.7

# Good to Know
Be advised that the logging level is pretty detailed, being hourly, and per target Host and Destination Port number,
and IP protocol.This means that it creates a pretty detailed picture of what the hosts on 
your LAN have been up to. Time permitting, in the future, I intend to make this retain only the last one month 
worth of details and compile the data into aggregate form for historical reporting to minimise the privacy impact. 

Naturally, you should take every necessary precaution to protect the database from unauthorised access via the access control
mechanisms provided by Mysql and your firewall of choice. 

Per the detailed logging, disk usage can be high (although not high by modern standards) -- example, on a 32-host LAN,
the mysql database has consumed about 530MB of disk space over the past 4 months without any form of database maintenance and pruning. This
however depends a bit on the amount of distinctive hosts and ports that the hosts on the LAN communicate with.

With data aggregation and pruning functionality in the future, disk usage should be lower.

If you require some config scripts for traffic shaping and to setup a router, they are found in the batch directory as 'router-rules.sh'
and 'trafficshaper.sh'. 