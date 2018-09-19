<?php

require('vendor/autoload.php'); 

use andreskrey\Readability\HTMLParser;
use andreskrey\Readability\Configuration;


$readability = new andreskrey\Readability\Readability(new Configuration());

$html = file_get_contents('http://ent.sina.com.cn/zl/bagua/2017-12-22/doc-ifypxrpp3324384.shtml');

try {
    $readability->parse($html);
    //echo $readability;
	//echo "<hr/>";
	//echo $readability->getTitle();
	//echo "<hr/>";
	//echo $readability ->getContent();
	echo "<hr/>";
	echo $readability->getExcerpt();
	//echo "<hr/>";
	//echo $readability->getImage();
	//echo "<hr/>";
	//var_dump($readability->getImages());
	//echo "<hr/>";
	//echo $readability->getAuthor();
	//echo "<hr/>";
	//echo '<pre>';print_r($readability->getImages());echo '</pre>';
/*	foreach($readability->getImages() as $val){
		echo '<img src="'.$val.'">';
	}*/
} catch (ParseException $e) {
    echo sprintf('Error processing text: %s', $e->getMessage);
}
