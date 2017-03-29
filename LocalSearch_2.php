<?php
  $address         =    $_GET['address'];
  $business        =    $_GET['business'];
  $distance        =    $_GET['distance'];

  $business_name   =    strstr($business,',',true);
  $business_code   =    trim(strstr($business,','),',');

  /*GoogleMapsAPIからxml形式でファイル取得*/
  $req  = 'http://maps.google.com/maps/api/geocode/xml';
  $req .= '?address='.urlencode($address);
  $req .= '&sensor=false';
  $req .= '&language=ja';
  $xml  = simplexml_load_file($req) or die(' ');
  $xml_array = get_object_vars($xml);
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <title>住所、都道府県市町村、ランドマーク</title>
  <link rel="stylesheet" type="text/css" href="LocalSearch.css">
</head>

<body>
  <?php
    /*nullだったら検索結果は複数*/
    if($xml_array['result']->formatted_address == NULL)
    {
  	/*検索結果が複数の場合*/
  			for($i = 0; $i < count($xml_array['result']); $i++)
        {
  				$name           [$i]  =   trim(strstr($xml_array['result'][$i]->formatted_address,' '), ' ');
  				$name           [$i]  =   trim(strstr($name[$i],' '), ' ');
  				$address_lat    [$i]  =   $xml_array['result'][0]->geometry->location->lat;
  				$address_lng    [$i]  =   $xml_array['result'][0]->geometry->location->lng;
  				$LocalSearch_url[$i]  =   "LocalSearch.php?lat=".$address_lat[$i]."&lng=".$address_lng[$i]."&business_code=".$business_code."&distance=".$distance;
  			}

    }
    /*検索結果が一つの場合*/
    else{
    			$name           [0]   =   trim(strstr($xml_array['result']->formatted_address,' '), ' ');
    			$address_lat    [0]   =   $xml_array['result']->geometry->location->lat;
    			$address_lng    [0]   =   $xml_array['result']->geometry->location->lng;
    			$LocalSearch_url[0]   =   "LocalSearch_3.php?lat=".$address_lat[0]."&lng=".$address_lng[0]."&business_code=".$business_code."&distance=".$distance;
  		}
  ?>

  検索カテゴリー：<?php echo strstr($business,',',true);?>
  <span class="br"></span>
  距離（半径）：<?php echo $distance;?>km
  <span class="br"></span>
  住所、ランドマーク
  <span class="br"></span>

  <!-- 検索結果を表示 -->
  <?php for($i = 0; $i < count($name); $i++):?>
  	<a href = <?php echo $LocalSearch_url[$i];?>> <?php echo $name[$i];?> </a>
  	<span class = "br"></span>
  <?php endfor;?>
</body>
</html>
