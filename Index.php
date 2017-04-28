<?php
/**
Basically, DATAPOINTS (defined below) (currently: /home/oceancolor/glider197/data/datapoints)
is the file parsed for locations to build the kml file.  So empty that file and the data points dissapear :-)

That file is built automatically from the "unit" files in that same directory.  So next 
step is to drop those unit* files into another folder so that their data is not re-introduced 
into datapoints the next time a new unit file is dropped in the folder.

Biggest issues in the past to the data flow have been permissions/selinux issues.
Should document that flow a bit more here at some point.  Quick hit (to refine later):
* emails from field system are sent to email server
* email server runs a script with the data which deposits the files in DATAPOINTS
* same script parses out the one line entry and puts that line in datapoints
* glider website runs, building an XML file from the datapoints to display them on the website using google API
**/

set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/local/lib/site-php');

define('DATAPOINTS',"../data/datapoints");

require_once("htmlDoc.php");
require_once("xmlUtils.php");
// $h = new htmlDoc("Glider 197","");
// $h->css("css/glider.css");
// $h->beg();

class dataProc {
    function __construct($file){
        $lines = file($file);
        $xml = new myXML("kml");
        $xml->addAttr("xmlns",'http://www.opengis.net/kml/2.2');
        //$xml->addAttr("xmlns",'http://earth.google.com/kml/2.2');


        $doc = $xml->addChild("Document");
        $style = $doc->addChild("Style");
	//$style = $xml->addChild("Style");
        $style->addAttr("id","glider");
        $iconstyle = $style->addChild("IconStyle");
        //$iconstyle->addChild("scale","0.7");
        $icon = $iconstyle->addChild("Icon");
        $icon->addChild("href","http://glider197.eri.ucsb.edu/images/glider-y.png");
        
        foreach($lines as $line){
            // datestamp, unit, lat, lon, filename, time offset
            $f = explode(",",$line);
            $lat = $this->fixCoord($f[2]);
            $lon = $this->fixCoord($f[3]);
            $pm = $doc->addChild("Placemark");
            //$pm = $xml->addChild("Placemark");
            //$pm = new xmlNode("Placemark");
            $pm->addChild("name",$f[1] . "-" . $f[0]);
            $pm->addChild("styleUrl","#glider");
            $df = "../data/" . $f[4];
            $df_string = "";
            if( file_exists($df)){
                $df_string = file_get_contents($df);
            }
            $pm->addChild("description","<![CDATA[\n" . "$df_string" . "]]>");
            $pt = $pm->addChild("Point");
            $pt->addChild("coordinates",$lon . "," . $lat);
        }
        
        $xml->outputFile("test.kml");
        
    }
    // takes the funky format from the email which is DDMM.MMMM 
    // to DD.DDDDDDDD
    function fixCoord($val){
        if( preg_match("/^([+-]*)(\d{2,3})(\d{2}\.\d{3})$/",$val,$m)){
            $deg=$m[2];
            $min=$m[3];
            $nmin= $min/60.0;
            $ndeg = $deg + $nmin;
            //print "$deg  $min<br />\n";
            return sprintf("%s%.6f",$m[1],$ndeg);
        }
        else {
            print "bad format on coordinate string: $val<br />\n";
        }
    }
}

$f = new dataProc(DATAPOINTS);
// print "<p>The link below no longer works since Googles change in maps.google.com usage in April 2015.</p>\n";
// print "<p>As of Feb 2016, we will be revamping the site to use the Google API Javascript libraries.</p>\n";
// $h->a_br("http://maps.google.com/maps?q=http://glider197.eri.ucsb.edu/kml/test.kml");
// $h->end();
?>
<!DOCTYPE HTML>
<!--
	Strata by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Glider 197</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
		<link rel="stylesheet" href="assets/css/main.css" />
		<!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
		<!-- map -->
		<style type="text/css">
			#map {
				height: 300px;
				width: 100%;
			}
			div#cdata {
				overflow-y: scroll;
				text-align: left;
				width: 100%;
			}
			pre {
				color: #000;
				font-size: 14px;
				overflow-x: scroll;
			}
			#main {
				width: calc(100%-30%);
			}
		</style>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvQMP93WyN1tIhBdQ2-y-BlaUIFOPqYpY"></script>
		<script src="map.js"></script>
	</head>
	<body id="top">

		<!-- Header -->
			<header id="header">
				<a href="#" class="image avatar"><img src="images/glider-y.png" alt="" /></a>
				<h1><strong>Glider 197</strong> </h1>
				<span>This map displays the current location data for Glider 197. Click on the marker on the map to display Glider logs.</span>
				<div id="glider-information"></div>
			</header>

		<!-- Main -->
			<div id="main">

				<!-- One -->
					<section id="one">
						<header class="major">
							<h2>Glider 197</h2>
						</header>
					</section>

				<!-- map -->
					<div id="map"></div>
					<br /><br />
					<div id="cdata">
						<h5>Glider logs:</h5>
						<pre id="content-window"></pre>
					</div>


					<section id="three">
						<h2>Contact</h2>
						<div class="row">
							<article class="6u 12u$(xsmall) work-item">
								<ul class="labeled-icons">
									<li>
										<h3 class="icon fa-home"><span class="label">Address</span></h3>
										Earth Research Institute<br />
										6832 Ellison Hall, University of California<br />
										Santa Barbara, CA 93106-3060
									</li>
								</ul>
							</article>
							<article class="6u 12u$(xsmall) work-item">
								<ul class="labeled-icons">
									<li>
										<h3 class="icon fa-mobile"><span class="label">Phone</span></h3>
										805/893-4885
									</li>
									<li>
										<h3 class="icon fa-envelope-o"><span class="label">Email</span></h3>
										<a href="MAILTO:webmaster@eri.ucsb.edu">webmaster@eri.ucsb.edu</a>
									</li>
								</ul>
							</article>
						</div>
					</section>
			</div>



		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.poptrox.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
			<script src="assets/js/main.js"></script>
		<!-- Footer -->
		<footer id="footer">
			<ul class="copyright">
				<li>&copy; UCSB Earth Research Institute</li>
				<li>Theme: <a href="http://html5up.net" target="_blank">HTML5 UP</a></li>
			</ul>
		</footer>



	</body>
</html>
