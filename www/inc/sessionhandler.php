<?php
// THESE NEED SETTING FOR EACH APPLICATION / INSTANCE:
$sess_dbcon='dbconfig.php'; // Database connection file
$sess_myslqi=$mysqli; // Set variable in above file for database connection
$sess_locknumber=5; // How many attempts before locking out
$sess_locktime=20; // How long are they locked out for in minutes
$sess_expires=20; // How long until a session expires
// END SETTINGS - DO NOT EDIT BELOW THIS LINE

if (!$sess_myslqi) { require_once($sess_dbcon); }

class SysSession implements SessionHandlerInterface
{
	private $link;
   
    public function open($savePath, $sessionName)
    {
		global $mysqli; // *** MYSQLI CONNECTION VARIABLE FROM DATABASE CONNECTION FILE *** 
		$link = $mysqli;
        if($link){
            $this->link = $link;
            return true;
        }else{
            return false;
        }
    }
    public function close()
    {
        mysqli_close($this->link);
        return true;
    }
    public function read($id)
    {
        $result = mysqli_query($this->link,"SELECT Session_Data FROM _sessions WHERE Session_Id = '".$id."' AND Session_Expires > '".date('Y-m-d H:i:s')."'");
        if($row = mysqli_fetch_assoc($result)){
            return $row['Session_Data'];
        }else{
            return "";
        }
    }
    public function write($id, $data)
    {
        $DateTime = date('Y-m-d H:i:s');
        $NewDateTime = date('Y-m-d H:i:s',strtotime($DateTime.' + 20 minutes'));
        $result = mysqli_query($this->link,"REPLACE INTO _sessions SET Session_Id = '".$id."', Session_Expires = '".$NewDateTime."', Session_Data = '".$data."'");
        if($result){
            return true;
        }else{
            return false;
        }
    }
    public function destroy($id)
    {
        $result = mysqli_query($this->link,"DELETE FROM _sessions WHERE Session_Id ='".$id."'");
        if($result){
            return true;
        }else{
            return false;
        }
    }
    public function gc($maxlifetime)
    {
        $result = mysqli_query($this->link,"DELETE FROM _sessions WHERE ((UNIX_TIMESTAMP(Session_Expires) + ".$maxlifetime.") < ".$maxlifetime.")");
        if($result){
            return true;
        }else{
            return false;
        }
    }
}
$handler = new SysSession();
session_set_save_handler($handler, true);

$DateTime = date('Y-m-d H:i:s');
$NewDateTime = date('Y-m-d H:i:s',strtotime($DateTime.' + '.$sess_expires.' minutes'));

session_start();
$_SESSION['REMOTE_ADDR']=$_SERVER['REMOTE_ADDR'];
$_SESSION['EXPIRES']=$NewDateTime;
$sess_ip=$_SESSION['REMOTE_ADDR'];
$sess_date=date('Y-m-d H:i:s',strtotime($DateTime.' - '.$sess_locktime.' minutes'));
?>