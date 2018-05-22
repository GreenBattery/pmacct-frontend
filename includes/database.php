<?php
/**
 * Basic database singleton
 * @author Daniel15 <daniel at dan.cx>
 */
class Database
{
	protected static $db;
	protected static $host;
	protected static $dbname ;
	protected static $username;
	protected static $password;

	protected static $engine = "mysql"; //what PDO engine/database is in use? Set this variable as needed
	/**
	 * Set the details used to connect to the database
	 * @param	string	Database server name
	 * @param	string	Database name
	 * @param	string	Username
	 * @param	string	Password
	 */
	public static function setDetails($host, $dbname, $username, $password, $engine='mysql')
	{
		self::$host = $host;
		self::$dbname = $dbname;
		self::$username = $username;
		self::$password = $password;
		self::$engine = $engine;
	}
	
	/**
	 * Get the database singleton
	 * @return The one PDO instance
	 */
	public static function getDB()
	{
		if (self::$db == null)
		{
		    if (self::$engine === "sqlite") {
		        self::$db = new PDO(self::$engine . ":" . self::$dbname);
            }else {
		        syslog(LOG_NOTICE, "stats: using mysql connection");
                self::$db = new PDO(self::$engine . ':host=' . self::$host . ';dbname=' . self::$dbname, self::$username, self::$password);
            }
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		return self::$db;
	}
	
	/**
	 * Format a date for use in database WHERE clauses
	 */
	public static function date($time)
	{
		return date('Y-m-d H:i:s', $time);
	}
}

// Set details based on config
Database::setDetails(Config::$database['host'], Config::$database['dbname'], Config::$database['username'], Config::$database['password']);
?>