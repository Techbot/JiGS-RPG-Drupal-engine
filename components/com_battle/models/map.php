<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
jimport( 'joomla.filesystem.folder' );
require_once JPATH_COMPONENT.'/helpers/messages.php';

class BattleModelMap extends JModel{

    function save_coord()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        //$update = JRequest::getVar('update');
        //if ($update==1){
        //$posx = JRequest::getVar('posx');
        //$posy = JRequest::getVar('posy');
        //$map_id = JRequest::getVar('id');
        $grid =	JRequest::getVar('grid');
        //$query = "UPDATE #__jigs_players SET map = '".$map_id."', grid = '".$grid."', posx = '".$posx."',posy = '".$posy."' WHERE id ='".$user->id."'" ;
        $query = "UPDATE #__jigs_players SET grid = '$grid' WHERE id ='$user->id'";
        $db->setQuery($query);
        $db->query();
        return;
        //}
    }


    function update_pos()
    {
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        //$update = JRequest::getVar('update');
        //if ($update==1){
        $posx = JRequest::getVar('posx');
        $posy = JRequest::getVar('posy');
        //$map_id = JRequest::getVar('id');
        //$grid =	JRequest::getVar('grid');
        //$query = "UPDATE #__jigs_players SET map = '".$map_id."', grid = '".$grid."', posx = '".$posx."',posy = '".$posy."' WHERE id ='".$user->id."'" ;
        $query = "UPDATE #__jigs_players SET posx = '$posx',posy = '$posy'  WHERE id ='$user->id'";
        $db->setQuery($query);
        $db->query();
        return;
        //}
    }





    function get_coord()
    {
        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();
        $db->setQuery("SELECT #__jigs_players.posx,
                #__jigs_players.posy,
                #__jigs_players.map,
                #__jigs_players.grid,
                #__comprofiler.avatar,
                #__jigs_players.active
                FROM #__jigs_players
                LEFT JOIN #__comprofiler
                ON #__comprofiler.user_id = #__jigs_players.id
                WHERE #__jigs_players.id =" . $user->id);
        $result = $db->loadRow();
        return $result;
    }


    /*old

    function get_grid()
    {

        $db         = JFactory::getDBO();
        $user       = JFactory::getUser();
        $db->setQuery("SELECT grid FROM #__jigs_players WHERE id =" . $user->id);
        $gridnumber = $db->loadResult();
        // echo $gridnumber;
        //$query      = "SELECT grid_index FROM #__jigs_maps WHERE grid = ".$gridnumber;
       // $db->setQuery($query);
        //$result     = $db->loadResult();
        return $gridnumber;
    }
    */

    function get_grid()
    {

        $db         = JFactory::getDBO();
        $user       = JFactory::getUser();
        $db->setQuery("SELECT  #__jigs_players.grid,
                #__jigs_players.posx,
                #__jigs_players.posy,
                #__comprofiler.avatar,
                #__jigs_players.active
                FROM #__jigs_players
                LEFT JOIN #__comprofiler
                ON #__comprofiler.user_id = #__jigs_players.id
                WHERE #__jigs_players.id =" . $user->id);

        $result = $db->loadRow();
        return $result;
    }


    function get_chars()
    {
        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();
        $query  = "SELECT grid FROM #__jigs_players WHERE id =" . $user->id;
        $db->setQuery($query);
        $grid   = $db->loadResult();
        $db->setQuery("SELECT * FROM #__jigs_characters WHERE grid = $grid AND active = 1 ");
        $result = $db->loadObjectlist();
        return $result;
    }

    function get_buildings()
    {
        $db     = JFactory::getDBO();
        $user   = JFactory::getUser();
        $db->setQuery("SELECT grid FROM #__jigs_players WHERE id = " . $user->id);
        $grid   = $db->loadResult();
        $db->setQuery("SELECT * FROM #__jigs_buildings WHERE grid = $grid");
        $result = $db->loadObjectlist();

        //add owner name to the result array
        foreach ($result as $building){
            $query=  "SELECT name FROM #__jigs_players WHERE id = $building->owner";
            $db->setQuery($query);
            $building->ownername = $db->loadResult();
        }
        $this->save_coord();
        return $result;
    }

    function get_pages()
    {
        $db =& JFactory::getDBO();
        $user =& JFactory::getUser();
        $db->setQuery("SELECT map,grid FROM #__jigs_players WHERE id =".$user->id);
        $result = $db->loadRow();
        $map = $result[0];
        $grid = $result[1];
        $db->setQuery("SELECT * FROM #__jigs_pages WHERE grid ='".$grid."' AND map='".$map."'");
        $result = $db->loadObjectlist();
        return $result;
    }

    function get_players()
    {
        $db		= JFactory::getDBO();
        $user	= JFactory::getUser();
        $db->setQuery("SELECT map,grid FROM #__jigs_players WHERE id =".$user->id);
        $result = $db->loadRow();
        $map	= $result[0];
        $grid	= $result[1];		
        $db->setQuery("SELECT #__jigs_players.id,

        #__jigs_players.name,
                #__jigs_players.posx,
                #__jigs_players.posy,
                #__comprofiler.avatar
                FROM #__jigs_players
                LEFT JOIN #__comprofiler ON #__jigs_players.id = #__comprofiler.user_id
                WHERE #__jigs_players.active = 1 AND #__jigs_players.grid ='".$grid."' AND #__jigs_players.map='".$map."' AND #__jigs_players.id !='".$user->id."'

                        ");
        $result = $db->loadObjectlist();
        return $result;
    }

}
