<?php
/*
 * Plugin Name: Wurmfarm Klima Monitor
 * Plugin URI: www.2komma5.org
 * Description: Zeigt die Temperatur, Luftfeuchtigkeit, Luftdruck, Bodenfeuchtigkeit, welche mit RaspberryPi und GrovePi+ gemessen wurden.
 * Version: 1.0.0
 * Author: Stefan Mayer
 * Author URI: http://www.2komma5.org
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


global $wpdb;
global $ws_db_version;
$ws_db_version = '1.0';
global $ws_plugin_version;
$ws_plugin_version = '1.0.0';
global $ws_table_name;
//setup of table name
global $ws_table_name;
$tablename        = 'ws_climadata';
//make it convertable for blog switching
//$wpdb->tables[]   = $tablename;
//prepare it for use in actual blog
$wpdb->$tablename = $wpdb->prefix . $tablename;
$ws_table_name    = $wpdb->$tablename;


add_filter('mce_external_plugins', "wormstation_register");
add_filter('mce_buttons', 'wormstation_add_button', 0);

function wormstation_add_button($buttons)
{
    array_push($buttons, "separator", "wormstation");
    return $buttons;
}

function wormstation_register($plugin_array)
{
    $url = plugins_url( 'editor_plugin.js', __FILE__ );
    $plugin_array['wormstation'] = $url;
    return $plugin_array;
}
//create db Table
function ws_create_plugin_table()
{
    global $wpdb;
    global $ws_db_version;
    global $ws_plugin_version;
    global $ws_table_name;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE " . $ws_table_name . "(
		`id` int(255) NOT NULL AUTO_INCREMENT,
		`temperature` double NOT NULL,
		`humidity` varchar(20) NOT NULL,
		`btemp` double NOT NULL,
		`pressure` varchar(11) NOT NULL,
		`altitude` double NOT NULL,
		`moisture` varchar(20) NOT NULL,
		`dateMeasured` date NOT NULL,
		`timeStamp` datetime NOT NULL,
		PRIMARY KEY (`id`)
	) " . $charset_collate . ";";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('ws_db_version', $ws_db_version);
    add_option('ws_plugin_version', $ws_plugin_version);
}
// delete db Table 
function ws_delete_plugin_table()
{
    
    global $wpdb;
    global $ws_table_name;
    
    //Delete any options thats stored also?
    delete_option('ws_plugin_version');
    delete_option('ws_db_version');
    $wpdb->query("DROP TABLE IF EXISTS $ws_table_name");
}

//Add JS loading to head
function ws_visualization_load_js()
{
    echo '<script type="text/javascript">';
    //echo 'google.load(\'visualization\', \'1.1\', {packages: [\'line\']});';
    echo 'google.load(\'visualization\', \'1\', {packages: [\'corechart\'],\'language\':\'de\'});';
    echo '</script>';
}

// Store the IDs of the generated graphs
$graphs_id = array();
// Create a DIV placeholder for the Visualization API

function ws_visualization_new_div($id, $width, $height)
{
    return "<div id=\"" . $id . "\" style=\"width: " . $width . "; height: " . $height . ";\"></div>";
}

function ws_read_db($options)
{
    global $graphs_id;
    global $wpdb;
    global $ws_table_name;
    // get data from db
    
    // create where condition depedence of date option
    $day_opt    = esc_sql($options[day]);
    $dateChosen = date('Y-m-d', esc_sql(strtotime($day_opt)));
    switch ($day_opt) {
        case "Today":
            $sql_where = "WHERE dateMeasured='" . $dateChosen . "'";
		    break;
        case "Yesterday":
            $sql_where = "WHERE dateMeasured='" . $dateChosen . "'";
		    break;
        case "Week":
            $dateToday   = date('Y-m-d');
            $dateWeekago = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $sql_where   = "WHERE dateMeasured BETWEEN '" . $dateWeekago . "' and '" . $dateToday . "'";
        	break;
        case "Month":
            $actMonth  = date('m');
            $sql_where = "WHERE MONTH(dateMeasured) = '" . $actMonth . "'";
        	break;
        case "Year":
            $actYear   = date('Y');
            $sql_where = "WHERE YEAR(dateMeasured) = '" . $actYear . "'";
        	break;
    }
    $sql = "SELECT * FROM " . $ws_table_name. " " . $sql_where;
    # read data from db	
    $resultSet = $wpdb->get_results($sql, ARRAY_A);
    //echo $sql, "nr:", $wpdb->num_rows;
    return $resultSet;
}
function set_title($options)
{
    global $graphs_id;
    global $wpdb;
    $month = array(1 => "Januar",
                2 => "Februar",
                3 => "März",
                4 => "April",
                5 => "Mai",
                6 => "Juni",
                7 => "Juli",
                8 => "August",
                9 => "September",
                10 => "Oktober",
                11 => "November",
                12 => "Dezember");
				
    $day_opt    = esc_sql($options[day]);
    $dateChosen = date('Y-m-d', esc_sql(strtotime($day_opt)));
    switch ($day_opt) {
        case "Today":
        	$options['title'] = $options['title'] . " - " . date('d.m.Y',strtotime($day_opt)); 
            break;
        case "Yesterday":
        	$options['title'] = $options['title'] . " - " . date('d.m.Y',strtotime($day_opt)); 
            break;
        case "Week":
            $dateToday   = date('Y-m-d');
            $dateWeekago = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 7, date("Y")));
            $options['title'] = $options['title'] . " - " . date('d.m.Y', strtotime($dateWeekago)) . " bis " . date('d.m.Y'); 
			break;
        case "Month":
            $options['title'] = $options['title'] . " - " . $month[date("n")];
			break;
        case "Year":
            $actYear   = date('Y');
            $options['title'] = $options['title'] . " - " . $actYear;
			break;
    }
    return $options;
}
//Generate a line chart
function ws_visualization_line_chart_shortcode($atts, $content = null)
{
    //use global variables
    global $graphs_id;
    global $wpdb;
	global $ws_table_name;
    $ws_options = shortcode_atts(array(
        'width' => "400px",
        'height' => "300px",
        'title' => "Graphx",
        'chart' => "Temp",
        'day' => "Today",
        'display' => "Temperatur",
        'scale' => "Celsius",
        'h_title' => "",
        'v_title' => "",
        
        //By default give iterated id to the graph
        'id' => "graph_" . count($graphs_id)
    ), $atts);
    
    //Register the graph ID
    $graphs_id[] = $ws_options['id'];
    
    //The content that will replace the shortcode
    $graph_content = "";
    
    //Generate the div
    $graph_content .= ws_visualization_new_div($ws_options['id'], $ws_options['width'], $ws_options['height']);
    
    //Generate the Javascript for the graph
    $graph_draw_js = "";
    
    $graph_draw_js .= '<script type="text/javascript">';
    $graph_draw_js .= 'function draw_' . $ws_options['id'] . '(){';
    
    // get data
    $resultSet = ws_read_db($ws_options);
    //Create the graph
    $graph_draw_js .= 'var data = new google.visualization.DataTable();';
    $chart = esc_sql($ws_options[chart]);
    if (0 == ($wpdb->num_rows)) {
        echo "no data in database";
    } else {
        if ('temp' == $chart) {
            $graph_draw_js .= 'data.addColumn("datetime","Zeit");';
            $graph_draw_js .= 'data.addColumn("number","Temperatur [C]");';
            $graph_draw_js .= 'data.addColumn("number","Luftfeuchte [%]");';
            $graph_draw_js .= 'data.addColumn("number","Barometer Temp [C]");';
        } elseif ('press' == $chart) {
            $graph_draw_js .= 'data.addColumn("datetime","Zeit");';
            $graph_draw_js .= 'data.addColumn("number","Luftdruck [hPa]");';
            //$graph_draw_js .= 'data.addColumn("number","Höhe [m]");';
        }
    }
    $day_opt = esc_sql($ws_options[day]);
    $graph_draw_js .= 'data.addRows([';
    $i = null;
    foreach ($resultSet as $row) {
        //echo $row['temperature']+"\r\n<br>";
        $dateMeasured = $row['dateMeasured'];
        $timeStamp    = $row['timeStamp'];
        $temperature  = $row['temperature'];
        $hum          = $row['humidity'];
        $btemp        = $row['btemp'];
        $pressure     = $row['pressure'];
        $altitude     = $row['altitude'];
        if ('temp' == $chart) {
            switch ($day_opt) {
                case "Today":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $temperature . ',' . $hum . ',' . $btemp . ']';
                    break;
                case "Yesterday":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $temperature . ',' . $hum . ',' . $btemp . ']';
                    break;
                case "Week":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $temperature . ',' . $hum . ',' . $btemp . ']';
                    break;
                case "Month":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $temperature . ',' . $hum . ',' . $btemp . ']';
                    break;
                case "Year":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $temperature . ',' . $hum . ',' . $btemp . ']';
                    break;
            }
        } elseif ('press' == $chart) {
            switch ($day_opt) {
                case "Today":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $pressure . ']'; //',' . $altitude . ']';
                    break;
                case "Yesterday":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $pressure . ']'; //',' . $altitude . ']';
                    $altitude . ']';
                    break;
                case "Week":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $pressure . ']'; //',' . $altitude . ']';
                    $altitude . ']';
                    break;
                case "Month":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $pressure . ']'; //',' . $altitude . ']';
                    $altitude . ']';
                    break;
                case "Year":
                    $graph_draw_js .= '[new Date("' . $timeStamp . '"),' . $pressure . ']'; //',' . $altitude . ']';
                    $altitude . ']';
                    break;
            }
            
        }
        $i = $i + 1;
        if ($i <> ($wpdb->num_rows)) {
            $graph_draw_js .= ',';
        }
    }
	$ws_options = set_title($ws_options);
    $graph_draw_js .= ']);';
    //Create the options
    $graph_draw_js .= 'var options = {';
    $graph_draw_js .= 'curveType: "function", ';
    $graph_draw_js .= 'animation: {duration: 1200, easing:"in"}, ';
    $graph_draw_js .= 'title:"' . $ws_options['title'] . '",';
    $graph_draw_js .= 'width:\'' . $ws_options['width'] . '\',';
    $graph_draw_js .= 'height:\'' . $ws_options['height'] . '\',';
    $graph_draw_js .= 'legend:\'bottom\',';
    $graph_draw_js .= 'backgroundColor: "transparent",';
    //if ($chart == 'press')
    // {
    //   $graph_draw_js .= 'series: { 0: {targetAxisIndex: 0}, 1: {targetAxisIndex: 1}},';
    //   $graph_draw_js .= 'vAxes: { 0: {title: "Luftdruck"}, 1: {title: "Höhe"}},';
    // }
    if (!empty($ws_options['h_title']))
        $graph_draw_js .= 'hAxis: {title: "' . $ws_options['h_title'] . '", slantedText:true},';
    
    if (!empty($ws_options['v_title'])) {
        if ('temp' == $chart) {
		    $sql = "SELECT temperature FROM " . $ws_table_name . " WHERE dateMeasured='" . $dateChosen . "' ORDER BY temperature ASC LIMIT 1";
        } elseif ('press' == $chart) {
			$sql = "SELECT pressure FROM " . $ws_table_name . " WHERE dateMeasured='" . $dateChosen . "' ORDER BY pressure ASC LIMIT 1";
        }
		$resultSet = $wpdb->get_results($sql);
        //echo $sql;  
        $graph_draw_js .= 'vAxis: {title: "' . $ws_options['v_title'] . '", viewWindow: {min:".$resultSet."}}';
    } else
        $graph_draw_js .= 'vAxis: {viewWindow: {min:-2}}';
    
    
    $graph_draw_js .= '};';
    //Populate the data
    
    $graph_draw_js .= 'var formatter = new google.visualization.DateFormat({pattern: "dd.MM.yyyy H:mm"}).format(data, 0);';
    $graph_draw_js .= 'var graph = new google.visualization.LineChart(document.getElementById(\'' . $ws_options['id'] . '\'));';
    //$graph_draw_js .= 'var graph = new google.charts.Line(document.getElementById(\'' . $ws_options['id'] . '\'));';
    
    $graph_draw_js .= 'graph.draw(data, options);';
    
    $graph_draw_js .= '}';
    $graph_draw_js .= '</script>';
    $graph_content .= $graph_draw_js;
    define("QUICK_CACHE_ALLOWED", false); //Quick Cache will not be caching the site displaying the measurements!
    return $graph_content;
}

//Filter to add JS to load all the graphs previously entered as shortcodes

function ws_visualization_load_graphs_js($content)
{
    //use global variables
    global $graphs_id;
    
    if (count($graphs_id) > 0) {
        $graph_draw_js = "";
        $graph_draw_js .= '<script type="text/javascript">';
        $graph_draw_js .= 'function draw_visualization(){';
        
        foreach ($graphs_id as $graph)
            $graph_draw_js .= 'draw_' . $graph . '();';
        
        $graph_draw_js .= '}';
        $graph_draw_js .= 'google.setOnLoadCallback(draw_visualization);';
        $graph_draw_js .= '</script>';
        
        //Add the graph drawing JS to the content of the post
        $content .= $graph_draw_js;
    }
    return $content;
}

//Add hook for front-end <head></head>
wp_register_script('jsapi', 'http://www.google.com/jsapi');
wp_enqueue_script('jsapi');

//create DB table
register_activation_hook(__FILE__, 'ws_create_plugin_table');
//register_deactivation_hook(__FILE__, 'ws_delete_plugin_table');

add_action('wp_head', 'ws_visualization_load_js');

//Add the short codes for the charts
add_shortcode('ws_chart', 'ws_visualization_line_chart_shortcode');

//Add filter to edit the contents of the post
add_filter('the_content', 'ws_visualization_load_graphs_js', 1000);
?>
