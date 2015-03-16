<?php
# Project Name: 	DB Functions
# File Name:    	db_functions.php
# File version:		v1.0.0
# Last updated:		19-Nov-2014
# Author:       	Laurens Hoogendijk
# Description: 		Functions for database connections
# Disclaimer: 		Only for use with FCVA systems

# Define constants
define( 'DB_HOST', 'rdbms.strato.de' ); 				# Database host
define( 'DB_USER', 'U2056795' ); 					# Database user
define( 'DB_PASS', '84Px5a!@' );	  				# Database password
define( 'DB_NAME', 'DB2056795' );	  				# Database name
define( 'LOG_ERROR_EMAIL', 'pokemon@martrauhe.nl' );			# Log email
define( 'LOG_SITE_NAME', 'pokemonFCVA' );				# Log this website
define( 'LOG_SITE_EXT', 'nl' );				# Site extention (com/info/ect)
define( 'DISPLAY_DEBUG', true );				# Display DB errors?

# Start of class
class DB
{
	private $link;
	public $filter;
	
	# Allow the class to send a message to admins. Message alerts them
	# of errors on production sites
    public function log_db_errors( $error, $query, $severity )
    {
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'To: Admin <'.LOG_ERROR_EMAIL.'>' . "\r\n";
        $headers .= 'From: '.LOG_SITE_NAME.' <system@'.LOG_SITE_NAME.'.'.LOG_SITE_EXT.'>' . "\r\n";
    
        $message = '<p>An error has occurred:</p>';

        $message .= '<p>Error at '. date('Y-m-d H:i:s').': ';
        $message .= 'Query: '. htmlentities( $query ).'<br />';
        $message .= '</p>';
        $message .= '<p>Severity: '. $severity .'</p>';

        mail( LOG_ERROR_EMAIL, 'Database Error', $message, $headers);

        if( DISPLAY_DEBUG )
        {
            echo $message;   
        }
    }
    
    # Constructor for the class
	public function __construct()
	{
	    global $connection;
		mb_internal_encoding( 'UTF-8' );
		mb_regex_encoding( 'UTF-8' );
		$this->link = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		
        if( mysqli_connect_errno() )
        {
            $this->log_db_errors( "Connect failed: %s\n", mysqli_connect_error(), 'Fatal' );
            exit();
        }
	}
	
	# Destructor (close connection)
	public function __destruct()
	{
		$this->disconnect();
	}
	
	
	# Clear input values
    public function filter( $data )
    {
        if( !is_array( $data ) )
        {
            $data = trim( htmlentities( $data ) );
        	$data = mysqli_real_escape_string( $this->link, $data );
        }
        else
        {
            # Self call function to sanitize array data
            $data = array_map( array( 'DB', 'filter' ), $data );
        }
    	return $data;
    }
    
    
    # Determine if common non-encapsulated fields are being used
    public function db_common( $value = '' )
    {
        if( is_array( $value ) )
        {
            foreach( $value as $v )
            {
                if( preg_match( '/AES_DECRYPT/i', $v ) || preg_match( '/AES_ENCRYPT/i', $v ) || preg_match( '/now()/i', $v ) )
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            if( preg_match( '/AES_DECRYPT/i', $value ) || preg_match( '/AES_ENCRYPT/i', $value ) || preg_match( '/now()/i', $value ) )
            {
                return true;
            }
        }
    }
    
    
	# Perform query
    public function query( $query )
    {
        $query = $this->link->query( $query );
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $query, 'Fatal' );
            return false; 
        }
        else
        {
            return true;
        }
        mysqli_free_result( $query );
    }
    
    
    # Check table existance
    public function table_exists( $name )
    {
        $check = $this->link->query("SELECT * FROM '$name' LIMIT 1");
        if( $check ) 
        {
            return true;
        }
        else
        {
            return false;
        }
        mysqli_free_result( $check );
    }
    
    
    # Count number of rows by query
    public function num_rows( $query )
    {
        $query = $this->link->query( $query );
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $query, 'Fatal' );
            return mysqli_error( $this->link );
        }
        else
        {
            return mysqli_num_rows( $query );
        }
        mysqli_free_result( $query );
    }
    
	
	# Check if value exists
	# 
	# Example Usage:
	# $check_user = array(
	#	 'user_email' => 'someuser@gmail.com',
	# 	 'user_id' => 48
	# );
	# $exists = exists( 'users', 'user_id', $check_user );
				
    public function exists( $table = '', $check_val = '', $params = array() )
    {
        if( empty($table) || empty($check_val) || empty($params) )
        {
            return false;
            exit;
        }
        $check = array();
        foreach( $params as $field => $value )
        {
            
            if( !empty( $field ) && !empty( $value ) )
            {
                # Check for frequently used mysql commands and prevent encapsulation of them
                if( $this->db_common( $value ) )
                {
                    $check[] = "$field = $value";   
                }
                else
                {
                    $check[] = "$field = '$value'";   
                }
            }

        }
        $check = implode(' AND ', $check);

        $rs_check = "SELECT $check_val FROM ".$table." WHERE $check";
    	$number = $this->num_rows( $rs_check );
        if( $number === 0 )
        {
            return false;
        }
        else
        {
            return true;
        }
        exit;
    }
    
	# Returns row based on query
    public function get_row( $query )
    {
        $query = $this->link->query( $query );
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $query, 'Fatal' );
            return false;
        }
        else
        {
            $r = mysqli_fetch_row( $query );
            mysqli_free_result( $query );
            return $r;   
        }
    }
    
	# Get results from query
    public function get_results( $query )
    {
        $row = array();
        $query = $this->link->query( $query );
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $query, 'Fatal' );
            return false;
        }
        else
        {
            while( $r = mysqli_fetch_array( $query, MYSQLI_ASSOC ) )
            {
                $row[] = $r;
            }
            mysqli_free_result( $query );
            return $row;   
        }
    }
    
    
    # Perform insert statement
    public function insert( $table, $variables = array() )
    {
        
        $sql = "INSERT INTO ". $table;
        $fields = array();
        $values = array();
        foreach( $variables as $field => $value )
        {
            $fields[] = $field;
            $values[] = "'".$value."'";
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';
        
        $sql .= $fields .' VALUES '. $values;

        $query = mysqli_query( $this->link, $sql );
        
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $sql, 'Fatal' );
            return false;
        }
        else
        {
            return true;
        }
    }
    
    # Insert data KNOWN TO BE SECURE into database table
    # Ensure that this function is only used with safe data
    # No class-side sanitizing is performed on values found to contain common sql commands
    # As dictated by the db_common function
    # All fields are assumed to be properly encapsulated before initiating this function
    public function insert_safe( $table, $variables = array() )
    {
        $sql = "INSERT INTO ". $table;
        $fields = array();
        $values = array();
        foreach( $variables as $field => $value )
        {
            $fields[] = $this->filter( $field );
            # Check for frequently used mysql commands and prevent encapsulation of them
            $values[] = $value; 
        }
        $fields = ' (' . implode(', ', $fields) . ')';
        $values = '('. implode(', ', $values) .')';
        
        $sql .= $fields .' VALUES '. $values;
        $query = mysqli_query( $this->link, $sql );
        
        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $sql, 'Fatal' );
            return false;
        }
        else
        {
            return true;
        }
    }
    
    
    # Perform update statement
    public function update( $table, $variables = array(), $where = array(), $limit = '' )
    {

        $sql = "UPDATE ". $table ." SET ";
        foreach( $variables as $field => $value )
        {
            
            $updates[] = "`$field` = '$value'";
        }
        $sql .= implode(', ', $updates);
        
        foreach( $where as $field => $value )
        {
            $value = $value;
                
            $clause[] = "$field = '$value'";
        }
        $sql .= ' WHERE '. implode(' AND ', $clause);
        
        if( !empty( $limit ) )
        {
            $sql .= ' LIMIT '. $limit;
        }

        $query = mysqli_query( $this->link, $sql );

        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $sql, 'Fatal' );
            return false;
        }
        else
        {
            return true;
        }
    }
    
    
    # Perform delete statement
    public function delete( $table, $where = array(), $limit = '' )
    {
        $sql = "DELETE FROM ". $table;
        foreach( $where as $field => $value )
        {
            $value = $value;
            $clause[] = "$field = '$value'";
        }
        $sql .= " WHERE ". implode(' AND ', $clause);
        
        if( !empty( $limit ) )
        {
            $sql .= " LIMIT ". $limit;
        }
            
        $query = mysqli_query( $this->link, $sql );

        if( mysqli_error( $this->link ) )
        {
            $this->log_db_errors( mysqli_error( $this->link ), $sql, 'Fatal' );
            return false;
        }
        else
        {
            return true;
        }
    }
    
    
    # Get autoincrement id
    public function lastid()
    {
        return mysqli_insert_id( $this->link );
    }
    
    
    # Get number of fields
    public function num_fields( $query )
    {
        $query = $this->link->query( $query );
        return mysqli_num_fields( $query );
        mysqli_free_result( $query );
    }
    
    # Get field names associated with a table
    public function list_fields( $query )
    {
        $query = $this->link->query( $query );
        return mysqli_fetch_fields( $query );
        mysqli_free_result( $query );
    }
    
    
    
    # Truncate tables
    public function truncate( $tables = array() )
    {
        if( !empty($tables) )
        {
            $truncated = 0;
            foreach( $tables as $table )
            {
                $truncate = "TRUNCATE TABLE `".trim($table)."`";
                mysqli_query( $this->link, $truncate );
                if( !mysqli_error( $this->link ) )
                {
                    $truncated++;
                }
            }
            return $truncated;
        }
    }
    
    
    # Output results of query
    public function display( $variable, $echo = true )
    {
        $out = '';
        if( !is_array( $variable ) )
        {
            $out .= $variable;
        }
        else
        {
            $out .= '<pre>';
            $out .= print_r( $variable, TRUE );
            $out .= '</pre>';
        }
        if( $echo === true )
        {
            echo $out;
        }
        else
        {
            return $out;
        }
    }
    
    
    # Disconnect (__destruct)
    public function disconnect()
    {
		mysqli_close( $this->link );
	}
}
?>