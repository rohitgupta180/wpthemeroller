<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class wrtDbOperations{
    private $user_custom_theme, $current__user_id;
    function __construct(){
        global $wpdb;
        $this->user_custom_theme = $wpdb->prefix.'user_custom_themes';
        $this->current_user_id = get_current_user_id();
    }
    public function createTableUserCustomThemes(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $user_custom_table_sql = "CREATE TABLE IF NOT EXISTS {$this->user_custom_theme} (
            id int(10) NOT NULL AUTO_INCREMENT, 
            user_id int(10) NOT NULL, 
            custom_styling varchar(2000) NOT NULL, 
            created_by int(10) NOT NULL, 
            created_on DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, 
            modified_by int(10), 
            modified_on DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, 
            PRIMARY KEY (id)) {$charset_collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($user_custom_table_sql);
    }

    public function saveUserStyleSheet($user_style_sheet){
        global $wpdb;
        $check_if_already_exists = $this->checkIfCurrentUserHasTheme();
        if($check_if_already_exists > 0){
            $wpdb->update($this->user_custom_theme, array('custom_styling'=>$user_style_sheet, 'modified_by'=>$this->current_user_id, 'modified_on'=>date("Y-m-d h:i:s")), array('user_id'=>$this->current_user_id));
        }else{
            $wpdb->insert($this->user_custom_theme, array('user_id'=>$this->current_user_id, 'custom_styling'=>$user_style_sheet, 'created_by'=>$this->current_user_id, 'created_on'=>date("Y-m-d h:i:s")), array('%d', '%s', '%d', '%s'));
        }
    }

    public function get_current_user_style_sheet(){
        global $wpdb;
        $style_sheet = $wpdb->get_var($wpdb->prepare("SELECT custom_styling FROM {$this->user_custom_theme} WHERE user_id=%d", $this->current_user_id));
        return $style_sheet;
    }
    
    function checkIfCurrentUserHasTheme(){
        global $wpdb;
        $style_sheet_count = $wpdb->get_var($wpdb->prepare("SELECT count(id) FROM {$this->user_custom_theme} WHERE user_id=%d", $this->current_user_id));
        return $style_sheet_count;
    }
}

$ct = new wrtDbOperations();
$user_custome_theme_tables = $ct->createTableUserCustomThemes();