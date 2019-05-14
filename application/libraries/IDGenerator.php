<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IDGenerator
{

    public static $instance;
    private $ci;
    private $key = 123456;
    private $max = 1;
    private $permissions = 0666;
    private $autoRelease = 1;

    private function __construct($params = array())
    {
        $this->ci =& get_instance();
        $this->ci->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    public static function getInstance()
    {
        if (self::$instance === NULL) {//if (!self::$instance){
            self::$instance = new IDGenerator();
        }
        return self::$instance;
    }

    function nextID($table)
    {
        $fp = fopen(FCPATH . "resource/files/lock/lock.txt", "w+");
        if (flock($fp, LOCK_EX)) { // lock
            $sequence = '';
            if (!$tempAllID = $this->ci->cache->get('idgenerator')) {
                $tempAllID = array();
                $query = $this->ci->db->get('app_table_sequence');
                foreach ($query->result() as $row) {
                    // update table
                    $tempAllID[$row->name] = $row;
                    if ($table == $row->name) {
                        $row = $tempAllID[$table];
                        $row->row++;
                        $row->last_insert_id = $row->prefix . str_pad($row->row, $row->pad, "0", STR_PAD_LEFT);
                        $this->ci->db->where('id', $row->id);
                        $this->ci->db->update('app_table_sequence', $row);
                        $this->ci->cache->save('idgenerator', $tempAllID);
                        $tempAllID[$table] = $row;
                        $sequence = $row;
                    }
                }
                $this->ci->cache->save('idgenerator', $tempAllID, 5);
            } else {
                if (isset($tempAllID[$table])) {
                    $row = $tempAllID[$table];
                    $row->row++;
                    $row->last_insert_id = $row->prefix . str_pad($row->row, $row->pad, "0", STR_PAD_LEFT);
                    $this->ci->db->where('id', $row->id);
                    $this->ci->db->update('app_table_sequence', $row);
                    $tempAllID[$table] = $row;
                    $sequence = $row;
                    $this->ci->cache->save('idgenerator', $tempAllID);
                }
            }
            flock($fp, LOCK_UN); // unlock
        } else {
            echo "failed";
        }
        fclose($fp);


        if (!empty($sequence)) {
            return $sequence->last_insert_id;
        } else {
            return '';
        }
    }

    function nextIDBatch($table, $count)
    {
        $sequence;
        if (!$tempAllID = $this->ci->cache->get('idgenerator')) {
            $tempAllID = array();
            $query = $this->ci->db->get('app_table_sequence');
            foreach ($query->result() as $row) {
                // update table
                $tempAllID[$row->name] = $row;
                if ($table == $row->name) {
                    $row = $tempAllID[$table];
                    $row->row += $count;
                    $row->last_insert_id = $row->prefix . str_pad($row->row, $row->pad, "0", STR_PAD_LEFT);
                    $this->ci->db->where('id', $row->id);
                    $this->ci->db->update('app_table_sequence', $row);
                    $this->ci->cache->save('idgenerator', $tempAllID);
                    $tempAllID[$table] = $row;
                    $sequence = $row;
                }
            }
            $this->ci->cache->save('idgenerator', $tempAllID, 5);
        } else {
            $row = $tempAllID[$table];
            $row->row += $count;
            $row->last_insert_id = $row->prefix . str_pad($row->row, $row->pad, "0", STR_PAD_LEFT);
            $this->ci->db->where('id', $row->id);
            $this->ci->db->update('app_table_sequence', $row);
            $tempAllID[$table] = $row;
            $sequence = $row;
            $this->ci->cache->save('idgenerator', $tempAllID);
        }
        // release
        flock($this->fp, LOCK_UN);
        fclose($this->fp);

        if (isset($sequence)) {
            return $sequence->last_insert_id;
        } else {
            return '';
        }
    }
}