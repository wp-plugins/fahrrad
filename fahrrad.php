<?php
/*
Plugin Name: Fahrrad
Plugin URI: http://wordpress.org/extend/plugins/fahrrad/
Description: Adds a customizeable widget which displays the latest news by http://www.fahrrad.net/
Version: 1.0
Author: Michael Maier
Author URI: http://www.fahrrad.net/
License: GPL3
*/

function fahrradnews()
{
  $options = get_option("widget_fahrradnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrrad',
      'news' => '5',
      'chars' => '30'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://news.google.de/news?pz=1&cf=all&ned=de&hl=de&q=fahrrad&cf=all&output=rss'); 
  ?> 
  
  <ul> 
  
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
    
    <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>"><?=$title?></a> 
    </li> 
    
    <?php 
    $cnt++;
  } 
  ?> 
  
  </ul>
<?php  
}

function widget_fahrradnews($args)
{
  extract($args);
  
  $options = get_option("widget_fahrradnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrrad',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  fahrradnews();
  echo $after_widget;
}

function fahrradnews_control()
{
  $options = get_option("widget_fahrradnews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Fahrrrad',
      'news' => '5',
      'chars' => '30'
    );
  }
  
  if($_POST['fahrradnews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['fahrradnews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['fahrradnews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['fahrradnews-CharCount']);
    update_option("widget_fahrradnews", $options);
  }
?> 
  <p>
    <label for="fahrradnews-WidgetTitle">Widget Title: </label>
    <input type="text" id="fahrradnews-WidgetTitle" name="fahrradnews-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="fahrradnews-NewsCount">Max. News: </label>
    <input type="text" id="fahrradnews-NewsCount" name="fahrradnews-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="fahrradnews-CharCount">Max. Characters: </label>
    <input type="text" id="fahrradnews-CharCount" name="fahrradnews-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="fahrradnews-Submit"  name="fahrradnews-Submit" value="1" />
  </p>
  
<?php
}

function fahrradnews_init()
{
  register_sidebar_widget(__('Fahrrrad'), 'widget_fahrradnews');    
  register_widget_control('Fahrrrad', 'fahrradnews_control', 300, 200);
}
add_action("plugins_loaded", "fahrradnews_init");
?>