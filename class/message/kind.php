<?php
if(!defined('ClassCms')) {exit();}
class message_kind {
    function all() {
        return array();
    }
    function groups() {
        $kinds=C('this:kind:all');
        $groups=array();
        foreach($kinds as $kind) {
            if($kind['groupname']=='' && !in_array($kind['groupname'],$groups)) {
                $groups[]='';
            }
        }
        foreach($kinds as $kind) {
            if(!in_array($kind['groupname'],$groups)) {
                $groups[]=$kind['groupname'];
            }
        }
        Return $groups;
    }
    function get($hash='',$classhash='') {
        if(!$classhash){
            $classhash=I(-1);
        }
        $kinds=C('this:kind:all');
        if($kinds && is_array($kinds)) {
            foreach($kinds as $kind) {
                if($kind['hash']==strtolower($hash) && $kind['classhash']==$classhash) {
                    Return $kind;
                }
            }
        }
        Return false;
    }
}