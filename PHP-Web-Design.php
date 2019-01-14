<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
	<title>HW6 of LiangweiZhao---Search Event</title>
	<style type="text/css">
		{
			margin:0;
			padding: 0;
		}
		.tableContainer{
			height: 100%;
			width: 100%;
			display: table;
			text-align: left;
		}
		.detailTxt{
			text-align: left;
			display: inline-block;
			max-width: 500px;
			max-height: 500px;
		}
		.detailImg{
			margin-top: 10px;
			margin-right: 90px;
			max-height: 350px;
			max-width: 450px;
			display: inline;
			float: right;
		}
		.subTitle{
			font-size: 18px;
			font-weight: bold;
			text-align: left;
			left: 0;
		}
		.subTxt{
			font-size: 16px;
			font-weight: 0;
			text-align: left;
			left: 0; 
		}
		.img1{
			display: block;
			max-height: 600px;
			max-width: 600px;
			width: auto;
			height: auto;
		}
		.img2{
			display: block;
			max-height: 400px;
			max-width: 450px;
			width: auto;
			height: auto;
		}
		.img3{
			display: block;
			max-height: 100px;
			max-width: 100px;
			width: auto;
			height: auto;
		}
		.map1{
			position: absolute;
			display: block;
			z-index: 5;
		}
		#map2{
			height: 90%;
			width: 60%;
		}
		.panel1{
			margin: 0;
			height: 90px;
			position: absolute;
			z-index: 10;
		}
		#panel2{
			display: block;
			float: left;
		}
		#trlMode{
			background-color: #E6E6E6;
			border: 0;
			font-size: 14px;
			position: absolute;
			margin-top: 50px;
			margin-left: 30px;
		}
		ul{
			list-style-type: none;
			padding: 0;
		}
		li:hover{
			background-color: #C6C6C6;
			color: gray;
		}
		a, a:visited{
			text-decoration: none;
			color:black;
		}
		h1{
			margin: 0;
		}
		table .tableTitle{
			height: 50px;
			width: 500px;
			text-align: center;
			padding-top: 10px;
			font-family: sans-serif;
			font-size: 35px;
		}
		table .tableContent{
			height: 110px;
			width: 500px;
			padding: 10px;
		}
		table .line{
			height: 2px;
			margin-left: 10px;
			width: 480px;
			background-color: #C6C6C6;
		}
		table.noRecords{
			height: 35px;
			width: 800px;
			background-color: #F6F6F6;
		}
	</style>
</head>
<body>
	<?php
		$keyword = $category = $locationRadio = $distance = $location = "";
		$loc_LAT = $loc_LONG = $segmentId = $id_Event = "";
		$tk_apikey = "YOUR API CODE";
		$gcp_apikey = "YOUR API CODE";
		$nameEvent = "";
		$arrContextOptions=array(
    			"ssl"=>array(
        			"verify_peer"=>false,
        			"verify_peer_name"=>false,
    			),
			); 
		date_default_timezone_set('Asia/Dhaka');
		if(isset($_GET['search'])){
			$keyword = $_GET['keyword'];
			$locationRadio = $_GET['locationRadio'];
			$category = $_GET['category'];
			$distance = $_GET['distance'];
			$loc_LAT = $_GET['lat'];
			$loc_LONG = $_GET['lon'];
			if($distance == '')
				$distance1 = 10;
			else
				$distance1 = $distance;
			$id_Event = $_GET["id"];
			if ($locationRadio == 'locNeed') {
				$location = $_GET['location'];
				$address_0 = urlencode($location);
				$url0 = "https://maps.googleapis.com/maps/api/geocode/json?address=$address_0&key=$gcp_apikey";
				$jsonGCP = file_get_contents($url0,false,stream_context_create($arrContextOptions));
				$gcpLoc = json_decode($jsonGCP,true)['results'][0]['geometry']['location'];
				$loc_LAT = $gcpLoc['lat'];
				$loc_LONG = $gcpLoc['lng'];
			}
			include 'geoHash.php';
			$geoPoint = encode($loc_LAT,$loc_LONG);
			if($category == 'music'){$segmentId = "KZFzniwnSyZfZ7v7nJ";}
			elseif($category == 'sports'){$segmentId = "KZFzniwnSyZfZ7v7nE";}
			elseif($category == 'artsAndTheatre'){$segmentId = "KZFzniwnSyZfZ7v7na";}
			elseif($category == 'film'){$segmentId = "KZFzniwnSyZfZ7v7nn";}
			elseif($category == 'miscellaneous'){$segmentId = "KZFzniwnSyZfZ7v7n1";}
			if(is_numeric($distance1)){
				$query = "apikey=$tk_apikey&geoPoint=$geoPoint&radius=$distance1&segmentId=$segmentId&unit=miles&keyword=$keyword";
				$url = "https://app.ticketmaster.com/discovery/v2/events.json?".$query; 
				$jsonFile = file_get_contents($url, false, stream_context_create($arrContextOptions));
			}
			sleep(1);
			if($id_Event != ""){
				$url2 = "https://app.ticketmaster.com/discovery/v2/events/".$id_Event.".json?apikey=".$tk_apikey;
				$jsonEvent = file_get_contents($url2, false, stream_context_create($arrContextOptions));
				$nameEvent = json_decode($jsonEvent,true)['_embedded']['venues'];
				if($nameEvent != null && $nameEvent[0]['name'] != null){
					sleep(1);
					$nameEncode = urlencode($nameEvent[0]['name']);
					$url3 = "https://app.ticketmaster.com/discovery/v2/venues.json?apikey=$tk_apikey&keyword=$nameEncode";
					$venueDetails = file_get_contents($url3,false,stream_context_create($arrContextOptions));
				}
			}
		}
	?>
	<form method="get" id="myForm" name="myForm">
		<div class="tableContainer">
			<table border = "5" align="center" style="background-color:#F6F6F6;border: 3px solid #C6C6C6;border-collapse: collapse;">
				<td>
				<div class="tableTitle">Events Search</div>
				<div class="line"></div>
				<div class="tableContent">
					Keyword<input type="text" name="keyword" value="<?php echo $keyword;?>" required>
					<br>
					Category<select name="category">
						<option value="default" <?php if($category == 'default') {echo "selected";}?>>Default</option>
						<option value="music" <?php if($category == 'music') {echo "selected";}?>>Music</option>
						<option value="sports" <?php if($category == 'sports') {echo "selected";}?>>Sports</option>
						<option value="artsAndTheatre" <?php if($category == 'artsAndTheatre') {echo "selected";}?>>Arts & Theatre</option>
						<option value="film" <?php if($category == 'film') {echo "selected";}?>>Film</option>
						<option value="miscellaneous" <?php if($category == 'miscellaneous') {echo "selected";}?>>Miscellaneous</option>
					</select><br>

					Distance(miles)<input type="text" name="distance" placeholder="10" value="<?php echo $distance;?>">from
					<input onclick="document.getElementById('locTxt').disabled = true;" 
					type="radio" name="locationRadio" value="here" id="here" <?php if($locationRadio=='here'||$locationRadio=='') {echo "checked";}?>>Here<br>

					<input onclick="document.getElementById('locTxt').disabled = false;" 
					type="radio" name="locationRadio" value="locNeed" style="margin-left: 290px;" <?php if($locationRadio=='locNeed') {echo "checked";}?>>
					<input id="locTxt" type="text" name="location" placeholder="location" <?php if($locationRadio!='locNeed'){echo "disabled";}?> value="<?php echo $location;?>" required><br>
					<input type="submit" name="search" id="search" value="search" style="margin-left: 50px;" onclick="<?php echo $_SERVER['PHP_SELF'];?>">
					<input type="button" name="clear" id="clear" value="clear">
				</div>
				</td>
			</table>
		</div>
	</form>
	<br><br>
	<div id = "demo" align="center"></div>
	<script type="text/javascript">
		function loadJSON(url){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.open("GET",url,false);
			xmlhttp.send();
			return xmlhttp.responseText;
		}
		var jsonDoc = loadJSON("http://ip-api.com/json");
		var jsonObj = JSON.parse(jsonDoc);
		var lat = jsonObj.lat;
		var lon = jsonObj.lon;
		<?php if(isset($_GET['search']) && $locationRadio != 'locNeed' && $loc_LAT == '' && $loc_LONG == ''){?>
			var curhref = window.location.href;
			var ipParams = curhref + "&lat=" + lat + "&lon=" + lon;
			window.location.href = ipParams;
			window.location.refresh();
		<?php } ?>
		<?php if($locationRadio == 'locNeed'){ ?>
			lat = parseFloat(<?php echo $loc_LAT;?>);
			lon = parseFloat(<?php echo $loc_LONG;?>);
		<?php } ?>
		var start = "";
		var end = "";
		var show = -1;
		var latVal = "";
		var lngVal = "";
		var directionsService = "";
		var directionsDisplay = "";
		var map = "";
		var trlMode = "";
		var lastClickID = -1;
		var WALKING = "WALKING";
		var BICYCLING = "BICYCLING";
		var DRIVING = "DRIVING";
		var resetListener = document.getElementById("clear");
		resetListener.addEventListener('click',function reset(){
			myForm = document.getElementById('myForm');
			myForm.keyword.value = "";
			myForm.category.selectedIndex = 0;
			document.getElementById('here').checked = "checked";
			myForm.location.value = "";
			myForm.distance.value = "";
			if(document.getElementById('demo') != null)
				document.getElementById('demo').innerHTML = "";
			if(document.getElementById('detail1') != null)
				document.getElementById('detail1').innerHTML = "";
			if(document.getElementById('detail2') != null)
				document.getElementById('detail2').innerHTML = "";
		});
		var txt = "";
		var jsonObj2 = <?php if(isset($jsonFile)){echo $jsonFile;} else{echo "null";}?>;
		var curURL = window.location.href;
		<?php if($id_Event == "" && isset($_GET['search'])){ ?>
			if(jsonObj2 == null || jsonObj2._embedded == null){
				txt = "<div><table class='noRecords' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
				txt += "<td style='text-align:center'>No Records has been found</td></table></div>";
			}
			else{
				var events = jsonObj2._embedded.events;
				if (events.length == 0){
					txt = "<div><table class='noRecords' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
					txt += "<td style='text-align:center'>No Records has been found</td></table></div>";
				}
				else{
					txt += "<form name=myForm2 method=get action='<?php $_SERVER['PHP_SELF']?>'>"
					txt += "<table border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0>";
					txt += "<tr>";
					txt += "<h2 style='font-weight:bold;'><td style='text-align:center;'>Date</td><td style='text-align:center;'>Icon</td><td style='text-align:center;'>Events</td><td style='text-align:center;'>Genre</td><td style='text-align:center;'>Venue</td></h2></tr>";
					for(var i = 0; i < events.length; i++){
						txt += "<tr>";
						if(events[i].dates.start.localDate == null && events[i].dates.start.localTime == null) //date and time
							txt += "<td style='width:80px;'>N/A</td>";
						else if(events[i].dates.start.localDate != null && events[i].dates.start.localTime != null)
							txt += "<td style='text-align:center;width:80px;'>" + events[i].dates.start.localDate + "<br>" + events[i].dates.start.localTime + "</td>";
						else if(events[i].dates.start.localDate == null)
							txt += "<td style='text-align:center;width:80px;'>" + events[i].dates.start.localTime + "</td>";
						else if(events[i].dates.start.localTime == null)
							txt += "<td style='text-align:center;width:80px;'>" + events[i].dates.start.localDate + "</td>";

						if(events[i].images == null) //icon
							txt += "<td style='width:120px;'>N/A</td>";
						else
							txt += "<td style='width:120px;' align=center><img class='img3' src='" + events[i].images[0].url + "'></td>";

						if(events[i].name == null)   //event name
							txt += "<td style='width:500px;padding-left:15px;'>N/A</td>";
						else
							txt += "<td style='width:500px;padding-left:15px;'><a href='"+curURL+"&id="+events[i].id+"'>" + events[i].name + "</a></td>";

						if(events[i].classifications == null) //genre(need change)
							txt += "<td style='width:80px;padding-left:15px;'>N/A</td>"
						else
							txt += "<td style='width:80px;padding-left:15px;'>" + events[i].classifications[0].segment.name + "</td>";

						if(events[i]._embedded == null || events[i]._embedded.venues == null || events[i]._embedded.venues[0].name == null) //venue
							txt += "<td style='width:400px;padding-left:15px;'>N/A</td>"
						else{
							txt += "<td style='width:400px;padding-left:15px;'><a href='javascript:showMap(" + events[i]._embedded.venues[0].location.latitude + "," + events[i]._embedded.venues[0].location.longitude + "," + i + ")'>" + events[i]._embedded.venues[0].name + "</a>";
							txt += "<div class='map1' id='mapVen"+i+"'></div>";
							txt += "<div class='panel1' id='panelVen"+i+"'></div>";
							txt += "</td>";
						}
					}
					txt += "</table>";
					txt += "</form>"
				}
			}
		document.getElementById("demo").innerHTML = txt;
		<?php }else{ ?>
		var jsonEveObj = <?php echo ($jsonEvent != "")? $jsonEvent:"null";?>;
		if(jsonEveObj == null){
			txt = "<div><table class='noRecords' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
			txt += "<td style='text-align:center'>No Details have been found</td></table></div>";
		}else{
			var details = jsonEveObj;
			var venueDetails = "";
			var txt = "";
			txt += "<div align=center style='font-size:10px;width:400px;'><h1>"+details.name+"</h1></div><br>";
			txt += "<div class='detailTxt' align=center>";
			//Time
			txt += "<div class='subTitle'>Date</div><div class='subTxt'>" + details.dates.start.localDate;
			if(details.dates.start.localTime != null)
				txt += " " + details.dates.start.localTime;
			txt += "</div><br>";
			//Artist/Team
			if(details._embedded.attractions != null){
				txt += "<div class='subTitle'>Artist/Team</div><div class='subTxt'>";
				for(var i = 0; i < details._embedded.attractions.length; i++){
					if(i > 0)
						txt += " | ";
					txt += "<a target='_blank' href='" + details._embedded.attractions[i].url + "'>" + details._embedded.attractions[i].name + "</a>";
				}
				txt += "</div><br>";
			}
			//Venue
			if(details._embedded.venues != null && details._embedded.venues[0].name !=null){
				txt += "<div class='subTitle'>Venue</div>";
				txt += "<div class='subTxt'>" + details._embedded.venues[0].name + "</div><br>";
				venueDetails = <?php echo ($venueDetails == "")? "null":$venueDetails;?>;
			}
			//Classifications
			if(details.classifications != null){
				var kinds = details.classifications[0];
				var nums = 0;
				txt += "<div class='subTitle'>Genres</div>";
				txt += "<div class='subTxt'>";
				if(kinds.subGenre != null && kinds.subGenre.name != null && kinds.subGenre.name != 'Undefined'){
					nums ++;
					txt += kinds.subGenre.name;
				}
				if(kinds.genre != null && kinds.genre.name != null && kinds.genre.name != 'Undefined'){
					if(nums > 0)
						txt += " | " + kinds.genre.name;
					else
						txt += kinds.genre.name;
					nums ++;
				}
				if(kinds.segment != null && kinds.segment.name != null && kinds.segment.name != 'Undefined'){
					if(nums > 0)
						txt += " | " + kinds.segment.name;
					else
						txt += kinds.segment.name;
					nums ++;
				}
				if(kinds.subType != null && kinds.subType.name != null && kinds.subType.name != 'Undefined'){
					if(nums > 0)
						txt += " | " + kinds.subType.name;
					else
						txt += kinds.subType.name;
					nums ++;
				}
				if(kinds.type != null && kinds.type.name != null && kinds.type.name != 'Undefined'){
					if(nums > 0)
						txt += " | " + kinds.type.name;
					else
						txt += kinds.type.name;
					nums ++;
				}
				txt += "</div><br>";
			}
			//Price Ranges
			if(details.priceRanges != null && (details.priceRanges[0].min != null || details.priceRanges[0].max != null)){
				var price = details.priceRanges[0];
				txt += "<div class='subTitle'>Price Ranges</div><div class='subTxt'>";
				if(price.min != null){
					if(price.max != null)
						txt += price.min + " - " + price.max;
					else
						txt += "(Min_price) " + price.min;

				}else{
					if(price.max != null)
						txt += "(Max_price) " + price.max;
				}
				txt += "</div><br>";
			}
			//Ticket Status
			if(details.dates != null && details.dates.status != null){
				txt += "<div class='subTitle'>Ticket Status</div>";
				txt += "<div class='subTxt'>" + details.dates.status.code + "</div><br>";
			}
			//Buy Tickets
			if(details.url != null){
				txt += "<div class='subTitle'>Buy Ticket At:</div>";
				txt += "<div class='subTxt'><a target='_blank' href='" + details.url + "'>Ticketmaster</a></div><br>"; 
			}
			txt += "</div>";
			//Seat Map
			if(details.seatmap != null){
				txt += "<div class='detailImg'><img class='img2' src='" + details.seatmap.staticUrl +"'></div><br>"; //change within the radio
			}
			
			txt += "<p id='arrowTxt1' align=center style='color:gray;margin-top:30px;'>click to show venue info</p>"; 
			txt += "<img id='arrowImg1' src='http://csci571.com/hw/hw6/images/arrow_down.png' align=center width=40 height=15 onclick = 'showDetails1()'>";
			txt += "<div id='detail1'></div>";
			txt += "<p id='arrowTxt2' align=center style='color:gray;'>click to show venue photos</p>";
			txt += "<img id='arrowImg2' src='http://csci571.com/hw/hw6/images/arrow_down.png' align=center width=40 height=15 onclick = 'showDetails2()'>"; 
			txt += "<div id='detail2'></div>";
			document.getElementById("demo").innerHTML = txt;
		}
		<?php }?>

	function showDetails1(){
			var arrowTxt1 = "";
			var arrowImg1 = "";
			var txt1 = "";
			trlMode = "";
			arrowTxt1 = "<p>click to hide venue info</p>";
			document.getElementById("arrowTxt1").innerHTML = arrowTxt1;
			arrowImg1 = "http://csci571.com/hw/hw6/images/arrow_up.png";
			document.getElementById("arrowImg1").src = arrowImg1;
			document.getElementById("arrowImg1").onclick = function(){reset('1');};

			var arrowTxt2 = "click to show venue photos";
			document.getElementById('arrowTxt2').innerHTML = arrowTxt2;
			var arrowImg2 = "http://csci571.com/hw/hw6/images/arrow_down.png";
			document.getElementById('arrowImg2').src = arrowImg2;
			document.getElementById('arrowImg2').onclick = showDetails2;
			document.getElementById('detail2').innerHTML = "";

			if(venueDetails == ""){
				txt1 += "<br>";
				txt1 += "<div><table style='background-color:white;height:35px;width:800px;' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
				txt1 += "<td style='text-align:center;font-weight:bold;'>No Venue Info Found</td></table></div>";
			}else{
				ven = venueDetails._embedded.venues;
				if(ven == null || ven.length == 0){
					txt1 += "<br>";
					txt1 += "<div><table style='background-color:white;height:35px;width:800px;' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
					txt1 += "<td style='text-align:center;font-weight:bold;'>No Venue Info Found</td></table></div>";
				}else{
					txt1 += "<br>";
					txt1 += "<table border=1 cellspacing=0 cellpadding=0>";
					//Name
					if(ven[0].name != null){
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>Name</h1></td>";
						txt1 += "<td style='text-align:center;'>" + ven[0].name + "</td>";
						txt1 += "</tr>";
					}
					//Location Map
					if(ven[0].location != null){
						latVal = parseFloat(ven[0].location.latitude);
						lngVal = parseFloat(ven[0].location.longitude);
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>Map</h1></td>";
						txt1 += "<td align=center style='width:600px;height:350px;'>"
						txt1 += "<div id= 'panel2'><ul id='trlMode'><li><a href='javascript:calcRoute(WALKING)'>Walk there</a></li>";
						txt1 += "<li><a href='javascript:calcRoute(BICYCLING)'>Bike there</a></li><li><a href='javascript:calcRoute(DRIVING)'>Drive there</a></li>";
						txt1 += "</select></div>";
						txt1 += "<div id='map2'></div></td>'";
 						txt1 += "</tr>";
 					}
					//Address
					if(ven[0].address != null && ven[0].address.line1 != null){
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>Address</h1></td>";
						txt1 += "<td style='text-align:center;'>" + ven[0].address.line1 + "</td>";
						txt1 += "</tr>";
					}
					//City
					if(ven[0].city != null && (ven[0].city.name != null || ven[0].city.stateCode != null)){
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>City</h1></td>";
						if (ven[0].city.stateCode != null){
							if (ven[0].city.name != null) 
								txt1 += "<td style='text-align:center;'>" + ven[0].city.name + ", " + ven[0].city.stateCode + "</td>";
							else
								txt1 += "<td style='text-align:center;'>" + ven[0].city.stateCode + "</td>";
						}
						else{
							if(ven[0].city.name != null)
								txt1 += "<td style='text-align:center;'>" + ven[0].city.name + "</td>";
						}
						txt1 += "</tr>";
					}
					//PostalCode
					if(ven[0].postalCode != null){
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>Postal Code</h1></td>";
						txt1 += "<td style='text-align:center;'>" + ven[0].postalCode + "</td>";
						txt1 += "</tr>";
					}
					//Url
					if(ven[0].url != null && ven[0].name != null){
						txt1 += "<tr><td style='font-size:10px;text-align:right;'><h1>Upcoming Events</h1></td>";
						txt1 += "<td style='text-align:center;'><a target='_blank' href='" + ven[0].url + "'>" + ven[0].name + " Tickets</a>";
						txt1 += "</tr></table>";
					}
				}
			}
		document.getElementById("detail1").innerHTML = txt1;
		dirMap('map2');
		}
		function showDetails2(){
			var arrowTxt2 = "";
			var arrowImg2 = "";
			var txt2 = "";
	
			var arrowTxt1 = "click to show venue info";
			document.getElementById('arrowTxt1').innerHTML = arrowTxt1;
			var arrowImg1 = "http://csci571.com/hw/hw6/images/arrow_down.png";
			document.getElementById('arrowImg1').src = arrowImg1;
			document.getElementById('arrowImg1').onclick = showDetails1;
			document.getElementById('detail1').innerHTML = "";

			arrowTxt2 = "<p align=center style='color:gray;margin-top:30px;'>click to hide venue photos</p>";
			document.getElementById("arrowTxt2").innerHTML = arrowTxt2;
			arrowImg2 = "http://csci571.com/hw/hw6/images/arrow_up.png";
			document.getElementById("arrowImg2").src = arrowImg2;

			document.getElementById("arrowImg2").onclick = function(){reset('2');};
			if(venueDetails == "" || venueDetails == null){
				txt2 += "<br>";
				txt2 += "<div><table style='background-color:white;height:35px;width:800px;' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
				txt2 += "<td style='text-align:center;font-weight:bold;'>No Venue Photos Found</td></table></div>";
			}else{
				var imgs = venueDetails._embedded.venues;
				if(imgs != null && imgs[0].images != null){
					txt2 += "<br>";
					txt2 += "<table border=1 bordercolor=#C6C6C6 cellpadding=0 cellspacing=0>";
					for(var i=0; i < imgs[0].images.length; i++){
						txt2 += "<tr><td style='width:700px;' align=center>";
						txt2 += "<img class='img1' src='" + imgs[0].images[i].url + "'>";
						txt2 += "</td></tr>";
					}
					txt2 += "</table>";
				}else{
					txt2 += "<br>";
					txt2 += "<div><table style='background-color:white;height:35px;width:800px;' border=1 bordercolor=#C6C6C6 cellspacing=0 cellpadding=0 align=center>";
					txt2 += "<td style='text-align:center;font-weight:bold;'>No Venue Photos Found</td></table></div>";
				}
			}
			document.getElementById("detail2").innerHTML = txt2;
		}
		function reset(arrowNum){
			var arrowTxt = "";
			if(arrowNum == '1'){
				arrowTxt = "click to show venue info";
				document.getElementById('arrowTxt1').innerHTML = arrowTxt;
			}else if(arrowNum == '2'){
				arrowTxt = "click to show venue photos";
				document.getElementById('arrowTxt2').innerHTML = arrowTxt;
			}
			var arrowImg = "http://csci571.com/hw/hw6/images/arrow_down.png";
			if(arrowNum == '1'){
				document.getElementById('arrowImg1').src = arrowImg;
				document.getElementById('arrowImg1').onclick = showDetails1;
			}else if(arrowNum == '2'){
				document.getElementById('arrowImg2').src = arrowImg;
				document.getElementById('arrowImg2').onclick = showDetails2;
			}
			var txt0 = "";
			if(arrowNum == '1'){
				document.getElementById('detail1').innerHTML = txt0;
			}else if(arrowNum == '2'){
				document.getElementById('detail2').innerHTML = txt0;
			}
		}
		function dirMap(mapID){
			start = new google.maps.LatLng(lat,lon);
			end = new google.maps.LatLng(latVal,lngVal);
			directionsService = new google.maps.DirectionsService();
			directionsDisplay = new google.maps.DirectionsRenderer();
			var place = {lat: latVal, lng: lngVal};
			var mapOptions = {
				zoom:14,
				center:place
			}
			map = new google.maps.Map(document.getElementById(mapID),mapOptions);
			if(trlMode == ""){
				marker = new google.maps.Marker({position: place,map: map});
			}
			directionsDisplay.setMap(map);
		}
		function calcRoute(trlMode1){
			trlMode = trlMode1;
			if(trlMode != "")
				marker.setMap(null);
			var request = {
				origin: start,
				destination: end,
				travelMode: google.maps.TravelMode[trlMode]
			};
			directionsService.route(request,function(result,status){
				if(status == 'OK'){
					directionsDisplay.setDirections(result);
				}
			});
		}
		function showMap(latVen,lonVen,venNum){
			var curClickID = venNum;
			var venCurID = "mapVen" + venNum;
			var panelCurID = "panelVen" + venNum;

			if(curClickID == lastClickID){
				trlMode = "";
				document.getElementById(venCurID).innerHTML = "";
				document.getElementById(panelCurID).innerHTML = "";
				document.getElementById(venCurID).style.height = "0px";
				document.getElementById(venCurID).style.width = "0px";
				lastClickID = -1;
			}else{
				var txtp = "";
				if(lastClickID != -1){
					trlMode = "";
					var venLastID = "mapVen" + lastClickID;
					var panelLastID = "panelVen" + lastClickID;
					document.getElementById(venLastID).innerHTML = "";
					document.getElementById(panelLastID).innerHTML = "";
					document.getElementById(venLastID).style.height = "0px";
					document.getElementById(venLastID).style.width = "0px";
				}
				document.getElementById(venCurID).style.height = "280px";
				document.getElementById(venCurID).style.width = "360px";
				latVal = latVen;
				lngVal = lonVen;
				dirMap(venCurID);
				txtp += "<ul id='trlMode' style='margin:0;'><li style='width:80px;height:30px;' align=center><a href='javascript:calcRoute(WALKING);'>Walk there</a></li>";
				txtp += "<li style='width:80px;height:30px;' align=center><a href='javascript:calcRoute(BICYCLING);'>Bike there</a></li><li style='width:80px;height:30px;' align=center><a href='javascript:calcRoute(DRIVING);'>Drive there</a></li>";
				txtp += "</ul>";
				lastClickID = curClickID;
				document.getElementById(panelCurID).innerHTML = txtp;
			}
		}
	</script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR KEY"></script>
</body>
</html>


