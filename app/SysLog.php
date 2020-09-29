<?php

namespace App;


class SysLog 
{
    /**
	* LOG
    *
    * @param  String  $type  Type
    * @param  String  $function_name  Function Name
    * @param  Int  $user_id  UserID
    * @param  String  $note  Note
	* @return Array 
	* 
	*/
    public static function log($type,$group_id,$function_name,$user_id,$col1 =null,$col2=null,$col3=null,$col4=null,$col5=null){
        $systemlog = new \App\models\SystemLog;
        $systemlog->group_id = $group_id;
        $systemlog->type = $type;
        $systemlog->function_name = $function_name;
        $systemlog->user_id = $user_id;
        $systemlog->col1 = $col1;
        $systemlog->col2 = $col2;
        $systemlog->col3 = $col3;
        $systemlog->col4 = $col4;
        $systemlog->col5 = $col5;
        $systemlog->save();
        return $systemlog;

    }
    


}
