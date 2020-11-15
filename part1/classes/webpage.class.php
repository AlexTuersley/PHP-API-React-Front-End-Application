<?php
/**
* Creates an HTML webpage using the given params
* 
* @author Alex Tuersley
* 
*/
abstract class WebPage {
 //Class variables
 private $main; 
 private $pageStart;
 protected $header; 
 private $css; 
 private $footer; 
 private $pageEnd;

 /**
 *
 * @param $pageTitle - A string to appear as web page title
 * @param $css - link for a css file
 * @param $pageHeading1 - a string to appear as an <h1>
 * @param $footerText - footer text should include any html tags
 *
 */
 public function __construct($pageTitle, $pageHeading1, $footerText) {
   $this->main = "";
   $this->set_css();
   $this->set_pageStart($pageTitle,$this->css);
   $this->set_header($pageHeading1);
   $this->set_footer($footerText);
   $this->set_pageEnd();
 }

 /**
  * function sets the start of the HTML page
  * @param $pageTitle - the title of the page
  * @param $css - The path to the css file
  */
 private function set_pageStart($pageTitle,$css) {
   $this->pageStart = <<<PAGESTART
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8" />
 <title>$pageTitle</title>
 <link rel="stylesheet" href="$css">
</head>
<body>
PAGESTART;
 }

 /**
  * Sets the path to the css on the page
  */
 private function set_css() {
   $this->css = BASEPATH.CSSPATH; 
 }

 /**
  * function creates a header for the page
  * @param $pageHeading1 - string that is the header of the page
  */
 protected function set_header($pageHeading1) {
   $this->header = <<<HEADER
<header>
 <h1>$pageHeading1</h1>
</header>
HEADER;
 }

 /**
  * function sets up the main content of the page
  * @param $main - the content of the page that will be displayed
  */
 private function set_main($main) {
   $this->main = <<<MAIN
<main>
 $main
</main>
MAIN;
 }

 /**
  * functions sets the footer of the page
  * @param $footerText - the text that will appear in the footer of the page
  */
 private function set_footer($footerText) {
   $this->footer = <<<FOOTER
<footer>
 $footerText
</footer>
FOOTER;
 }

 /**
  * sets the end of the page with closing tags
  */
 private function set_pageEnd() {
   $this->pageEnd = <<<PAGEEND
</body>
</html>
PAGEEND;
 }

 /**
  * function adds content to the body of the page
  * @param $text - text that will be added to the main content of the page
  */
 public function addToBody($text) {
   $this->main .= $text;
 }

 /**
  * gets all the content from the other functions
  * @return HTML web page with all the content
  */
 public function get_page() {
   $this->set_main($this->main);
   return 
     $this->pageStart.
     $this->header.
     $this->main.
     $this->footer.
     $this->pageEnd; 
 }
}
?>