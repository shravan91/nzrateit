<html> 
<head style="width: 100%; height: 100%"> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  
  <title> OSM/Google Maps - Crowdsourcing Project - rateitNZ </title> 
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
</head> 
<body style="margin:0px; padding:0px;">
<form id="form1" method="post" action='rate.php' style="margin:0px; padding:0px;">

	<?php

	if(isset($_POST['rate']) && isset($_POST['id1']))
	{
		//Connection String to connect to the database using PHP
		
		$con=mysql_connect("localhost","root","") or die('Could not connect to server.' );
        mysql_select_db('nz', $con) or die('Could not select database.');
		
		//Queries to update, insert and get data from the database
		mysql_query("insert into user(bar_id,status,lat1,lon2) values(".$_POST['id1'].",'".$_POST['rate']."',".$_POST['lat'].",".$_POST['lon'].")") or die(mysql_error());
		
		//10 clicks on one place will change the marker from small to medium | medium to big
		
		$rs=mysql_query("SELECT count_rate FROM `people` where bar_id=".$_POST['id1']);
		for($i=0;$row=mysql_fetch_array($rs);$i++)
		{
			//echo $row[0];
			
			if($row[0]>=5)
				{
					mysql_query("update info set status ='yellow' where id=".$_POST['id1']);
				}
				if($row[0]>=10)
				{
					mysql_query("update info set status ='red' where id=".$_POST['id1']);
				}
		}
		$query=mysql_query("select * from  people where bar_id=".$_POST['id1']);
		if(mysql_num_rows($query)>0)
		{
			//$query=mysql_query("update people set count_rate=count_rate+1 where bar_id=".$_POST['id1']) or die(mysql_error());
		}
		else
		{
			//$q="insert into people(bar_id,count_rate) values(".$_POST['id1'].",1)";
			//echo $q; // for the popup
			$query=mysql_query($q) or die(mysql_error());
		}
		// pops up the feedback screen on the web page itself
				echo "<table  align='center' style='border: 5px solid #F52887;' cellspacing=0 cellpading=4><tr><td><font size=4 style='color:#C11B17'>Thank You for rating and being a part of this crowdsourcing project!</font></td></tr><tr align='center'><td><font size=3 style='color:#C11B17'>Found a <font style='color:#7D0552'><b>new place</b></font> or a <font style='color:#7D0552'><b>place which doesn't exist</b></font>: Click on the <b>Feedback Button</b> below and we'll change it</font></td></tr></table>";
			//header('Location: thankyou.html') ;
	}
	?>
    
 <!-- <div id="map" style="width: 500px; height: 400px;"></div>
-->
<!--Map Style-->

<div id="map" style="width: 100%; height: 100%"></div>

  <script type="text/javascript">
  var posLat,posLat;
  var locations=[];
  <?php
		$con=mysql_connect("localhost","root","") or die('Could not connect to server.' );
        mysql_select_db('nz', $con) or die('Could not select database.');
		//$query="select name,lat,lon,id,type,status from info;";			//changes begin here
		//Gets last time of rating and other things from people table
		$query="select i.name as name,i.lat,i.lon,id,i.type,i.status,IFNULL(p.trust,0),ifnull(max(u.date),0) from info i left outer join (people p,user u) on i.id=p.bar_id and u.bar_id=i.id group by i.id;";	
				//changes done for getting the date
		
		$set=mysql_query($query);
		$i=0;
		while($row=mysql_fetch_array($set))
		
		{
		?>
			<?php $name=$row[0]; ?>
    		locations[<?php echo $i;?>] =['<?php echo $name;?>',<?php echo $row[1];?>,<?php echo $row[2];?>, <?php echo $row[3];?>, '<?php echo $row[4];?>', '<?php echo $row[5];?>','<?php echo $row[6];?>','<?php echo substr("$row[7]", 5, 11);?>'];
			
		
		  <?php
		  $i++;
		}
		?>
		
		
			   var element = document.getElementById("map");
 
            /*
            Build list of map types.
            You can also use var mapTypeIds = ["roadmap", "satellite", "hybrid", "terrain", "OSM"]
            but static lists sucks when google updates the default list of map types.
            */
			
			//Getting the base Maps
            var mapTypeIds = [];
            for(var type in google.maps.MapTypeId) {
                										mapTypeIds.push(google.maps.MapTypeId[type]);
           										   }
           //Pushes OSM as the default map
		    									mapTypeIds.push("OSM");
			
	  var copyrights = {}
    var map = new google.maps.Map(document.getElementById("map"), {
     																 zoom: 19,
      																 center: new google.maps.LatLng(-43.53, 172.6203),
     																 mapTypeId: "OSM",
	  																 scaleControl: true,
																	 
                                                                     streetViewControl: true,
           															 panControl: true,
																	 overviewMapControl: true,
																	        overviewMapControlOptions: {
																										opened: true,
																										position: google.maps.ControlPosition.BOTTOM_CENTER 
																									   },
																			mapTypeControlOptions: {
																										mapTypeIds: mapTypeIds
																								   }
																  });
 
         
		 
		    map.mapTypes.set("OSM", new google.maps.ImageMapType({
                													getTileUrl: function(coord, zoom) {
                    												                                   return "http://otile1.mqcdn.com/tiles/1.0.0/map/" + zoom 																	+ "/" + coord.x + "/" + coord.y + ".png";
																	                                   // .png gets the tiles of OSM
                												                                      },
																	                                   tileSize: new google.maps.Size(256, 256),
																	                                   name: "OSM",
																	                                   maxZoom: 20
																 }
			                                                     ));
			

			
//Get Geo location using HTML 5 - A New way of using HTML5 Technology to get location of User
if(navigator.geolocation) {
								navigator.geolocation.getCurrentPosition(function(position)
								{
										var pos = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
										var image = 'Images/user1.png';
										posLat=position.coords.latitude;		
										posLon=position.coords.longitude;

										//Maker options for user location
										var marker = new google.maps.Marker({
                                              //	position: pos,
	  										//	icon: image,
											//	animation: google.maps.Animation.DROP, 
											//	map: map,
	 										});
      										map.setCenter(pos);
    }, 
	function() 
	{
      handleNoGeolocation(true);
    });
                       } 


    var infowindow = new google.maps.InfoWindow(
	{
		maxWidth: 800
	}
	);

    //var image = 'Images/bar_blue.png';
    var marker, i;

    for (i = 0; i < locations.length; i++) {  
	
	// Populates the map with the data from the info table in the database
	
	var image="images/bar_"+locations[i][5]+".png";
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
		icon: image,
		id: locations[i][3]
      });
	 
	 
	 
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
			var html = "<table style=' border-bottom: 6px solid black; width: 380px;'>" +
				
                 "<tr><td><b>Place Name :</td> <td> "+locations[i][0]+"</td> </tr>" +
                "<tr><td><b>Place Type :</td> <td> "+locations[i][4]+"</td> </tr>" +			 			 

		// New displays on infowindow displaying time of last rating and trust rating
		
				"<tr><td><b>Trust Level :</b></td> <td> "+locations[i][6]+"%"+"</td> </tr>" +
				//"<tr><td><b>Time Of Last Rating :</b></td> <td> "+'$me'+"</td> </tr>" +
				"<tr><td><b>Time Of Last Rating :</b></td> <td> "+locations[i][7]+"</td> </tr>" +
				"<tr><td><b>Current Rating :</b></td> <td> "+"<img src=images/bar_"+locations[i][5]+".png></img>"+"</td> </tr>" +
				
				
				  "<table style='width: 380px;'>"+
"<td><b>Select New Rating :</b> </td><td style ='border: 3px solid black;'><input type='image' name=green onclick=setVal('green',"+i+") img src='images/bar_green.png' alt='green' border='0'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='image' name=yellow onclick=setVal('yellow',"+i+") img src='images/bar_yellow.png' alt='yellow'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type='image' name=red onclick=setVal('red',"+i+") img src='images/bar_red.png' alt='red'> </td>"+
				"</tr>"+
				//
				
				"</table>"
				"</table>";
          infowindow.setContent(html);
          infowindow.open(map, marker);
//		  alert(this.position+" ok "+this.get("id"));
        }
      })(marker, i));
	    function setVal(rate,i){
		var form = document.getElementById("form1");
		var hidden = document.createElement("input");
		hidden.type = "hidden";
		hidden.name = "rate";
		hidden.value = rate;
		form.appendChild(hidden);
		
		var hid = document.createElement("input");
		hid.type = "hidden";
		hid.name = "id1";
		hid.value = locations[i][3];
		form.appendChild(hid);
		
		var posLat1 = document.createElement("input");
		posLat1.type = "hidden";
		posLat1.name = "lat";
		posLat1.value = posLat;
		form.appendChild(posLat1);

		var posLon1 = document.createElement("input");
		posLon1.type = "hidden";
		posLon1.name = "lon";
		posLon1.value = posLon;
		form.appendChild(posLon1);
		
		form.submit();
		
			
		  }
	}
		
  </script>
  
  </form>
</body>
</html>