<?php
class HMSphinx  extends CComponent{
    private $host = '127.0.0.1';
    private $port = '9306';
    private $user = '';
    private $pass = '';
    private $db_name = '';
    private $char = 'utf8';
    private $db_link = '';
    public $error = false;
    
	function init($host = '', $user = '', $pass = '', $port = '') {
        if($host && $user && $pass && $port)
            $this->setServer($host, $user, $pass, $port);
        $this->conn();
    }

    function setServer($host, $user, $pass, $port){
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }
    
    function conn(){
        $this->db_link = mysql_connect($this->host.':'.$this->port, $this->user, $this->pass, true);        
        if (!$this->db_link) {
            $this->error($type=1);
            return false;
        }else{
          return   mysql_set_charset($this->char, $this->db_link);
        }
    }
    
    function query($sql){
        if($this->error)
            return false;
        mysql_select_db('hm_main');
        $result = mysql_query($sql);
        $return_result = array();
        if($result){
           if(preg_match('/^select(.*)/i', trim($sql))){
                while ($row = mysql_fetch_assoc($result)) {
                    $return_result[] = $row;
                }
                mysql_free_result($result);
          }else{
                $return_result = mysql_affected_rows($this->db_link);
          }
            return $return_result;
        }else{
            $this->error(2);
        }
    }
    
    
    public function error($type=''){ //Choose error type
        if (empty($type)) {
            return false;
        }else{
            $this->error = true;
            
            if ($type==1)
                echo "<strong>Database could not connect</strong> ";
            else if ($type==2)
                echo "<strong>mysql error</strong> " . mysql_error();
            else if ($type==3)
                echo "<strong>error </strong>, Proses has been stopped";
            else
                echo "<strong>error </strong>, no connection !!!";
        }
    }
    
    function __destruct() {
        if($this->db_link)
            mysql_close($this->db_link);
    }
}