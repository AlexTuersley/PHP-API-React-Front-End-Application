<?php 
/**
* Create a webpage with a navbar menu
* @author Alex Tuersley
*/
class WebPageWithNav extends WebPage {
 
 private $nav;
 private $navItems;

 /**
  * function creates a header for the page
  * @param $pageHeading1 - string that is the header of the page
  */
 protected function set_header($pageHeading1) {
  $basepath = BASEPATH;
   $this->set_nav($basepath, ["home"=>"","documentation"=>"documentation/","about"=>"about/"]);
   $nav = $this->nav;
   $this->header = <<<HEADER
<header>
 <h1>$pageHeading1</h1>
 $nav
</header>
HEADER;
 }

 /**
  * function creates the navigation menu in html format
  * @param $listItems - array with all the headers for the menu
  */
 private function navHTML($listItems) {
   return <<<MYNAV
<nav>
<ul>
  $listItems
<ul>
</nav>
MYNAV;
 }

 /**
 * This generates the menu as an unordered list and 
 * then sets the nav property
 *
 * @param $basepath - the url path  
 * @param $navItems - an associative array with the keys 
 * as menu items and values as links
 */
private function set_nav($basepath, array $navItems) {
  $listItems = "";
  foreach ($navItems as $key => $value) {
    $listItems .= "<li><a href='$basepath$value'>$key</a></li>";
  }
  $this->nav = $this->navHTML($listItems);
}


}
?>