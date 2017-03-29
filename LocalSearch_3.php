<?php
  error_reporting(0);

  $local_api_url  = 'https://map.yahooapis.jp/search/local/V1/localSearch';
  $review_api_url = 'https://map.yahooapis.jp/olp/v1/review/';
  $yolp_appid     = <your_api_key>;
  $map_pins       =  array("img/pin_1.png","img/pin_2.png","img/pin_3.png","img/pin_4.png","img/pin_5.png","img/pin_6.png","img/pin_7.png","img/pin_8.png","img/pin_9.png","img/pin_10.png");

  $j=0;

  /*LocalSearchApiクエリ*/
  $lat   =   $_GET['lat'];
  $lon   =   $_GET['lng'];
  $dist  =   $_GET['distance'];
  $gc    =   $_GET['business_code'];
  $image =   true;
  $sort  =   'hybrid';

  /*LocalSearchApiからxml形式でファイル取得*/
  $local_req       =   $local_api_url.'?appid='.$yolp_appid.'&lat='.$lat.'&lon='.$lon.'&sort='.$sort.'&dist='.$dist.'&gc='.$gc.'&image=true'.'&results=30';
  $local_xml       =   simplexml_load_file($local_req);
  $local_xml_array =   get_object_vars($local_xml);
?>


<!DOCTYPE html>
<html>

  <head>
    <title>ローカルサーチ</title>
    <meta name    =  "viewport" content = "initial-scale=1.0">
    <meta charset =  "utf-8">
		<link rel     =  "stylesheet"  type = "text/css" href = "LocalSearch.css">
    <script src   =  "./smooth-scroll.js"></script>
  </head>

  <body>
    <?php
        /*検索結果がない場合*/
        if($local_xml_array['ResultInfo']->Total == 0)  goto skip;

        /*検索結果がある場合*/
        else
        {
          $result                   =     true;
          $result_count             =     $local_xml_array['ResultInfo']->Total;

          foreach($local_xml_array['Feature'] as $key => $content)
          {
            /*店舗情報取得*/
            $name     [$key]        =     $content[$key]->Name;
            $data     [$key]        =     $content[$key]->Geometry->Coordinates;
    		    $address  [$key]        =     $content[$key]->Property->Address;
            $uid      [$key]        =     $content[$key]->Property->Uid;
            $image_url[$key]        =     $content[$key]->Property->LeadImage;

            /*クチコミApiからxml形式でファイル取得*/
            $review_req             =     $review_api_url.$uid[$key].'?appid='.$yolp_appid;
            $review_xml             =     simplexml_load_file($review_req);
            $review_xml_array[$key] =     get_object_vars($review_xml);
          }

          foreach($review_xml_array as $key => $array)
          {
            /*口コミがあるとき*/
				    if($array['Feature']!=NULL)
            {
              $result_10_review [$j]  =  $array;
				 	    $result_10_address[$j]  =  $address    [$key];
    				 	$result_10_name   [$j]  =  $name       [$key];
              $result_10_image  [$j]  =  $image_url  [$key];
    					$result_10_lat    [$j]  =  trim(strstr($data[$key],','), ',');
    					$result_10_lon    [$j]  =  strstr($data[$key],',',true);
    				 	$j++;
            }
            /*クチコミがある店舗が10件見つかったら*/
            if($j === 10) break;
          }

          /*クチコミがない店舗を取得*/
          if($j !== 10)
          {
            foreach($review_xml_array as $key => $array)
            {
              if($array['Feature'] == NULL)
              {
    					 	$result_10_review [$j] =  $array;
    					 	$result_10_address[$j] =  $address    [$key];
    					 	$result_10_name   [$j] =  $name       [$key];
    	          $result_10_image  [$j] =  $image_url  [$key];
    						$result_10_lat    [$j] =  trim(strstr($data[$key],','), ',');
    						$result_10_lon    [$j] =  strstr($data[$key],',',true);
    					 	$j++;
              }
              /*クチコミがある店舗とクチコミがない店舗が合計10件見つかったら*/
              if($j === 10) break;
            }
          }
          skip:
        }
    ?>


<div id = "map"></div>

<article id = "result_lists">
  <h2>
		<?php
        if($result)
        {
            echo "検索結果が".$result_count."件見つかりました。";
            echo "<span class=\"br\"></span>";
            if($result_count>10)
            {
              echo "人気の10件を表示します。";
              echo "<span class=\"br\"></span>";
            }
        }

        else
        {
            echo "一致する検索結果がありませんでした。";
				    echo "<span class=\"br\"></span>";
        }
        echo "<span class=\"br\"></span>";
    ?>
	</h2>

  <!-- 店舗情報を表示 -->
	<?php for($i = 0; $i<count($result_10_name); $i++):?>
    <section>
      <!-- ピン画像 -->
      <img src = <?php echo $map_pins[$i]; ?> align = "left">
      <!-- 店舗名 -->
      <h3 id=<?php echo "\"".$i."\""; ?>><?php echo $result_10_name[$i];?></h3>
      <!-- 住所 -->
      <h3><?php echo $result_10_address[$i];?></h3>
      <!-- 画像 -->
      <img src = <?php echo $result_10_image[$i]; ?>>

      <p>
        <?php

            /*クチコミがない場合*/
            if($result_10_review[$i]['ResultInfo']->Count == '0')
            {
		        echo '<h3>'; echo "口コミはありません。"; echo '</h3>';
            }

            /*クチコミが一件の場合*/
            else if($result_10_review[$i]['ResultInfo']->Count == '1')
            {
              echo '<h3>'; echo "口コミ"; echo '</h3>';
              echo "<strong>";
              echo $result_10_review[$i]['Feature']->Property->Comment->Subject;
              echo "</strong>";
              echo "<span class=\"br\"></span>";
              echo $result_10_review[$i]['Feature']->Property->Comment->Body;
              echo "<span class=\"br\"></span>";
            }

            /*クチコミが複数の場合*/
            else{
              echo '<h3>'; echo "口コミ"; echo '</h3>';
              for($k = 0; $k<count($result_10_review[$i]['Feature']); $k++)
              {
                echo "<strong>";
                echo $result_10_review[$i]['Feature'][$k]->Property->Comment->Subject;
                echo "</strong>";
                echo "<span class=\"br\"></span>";
                echo $result_10_review[$i]['Feature'][$k]->Property->Comment->Body;
                echo "<span class=\"br\"></span>";
                echo "<span class=\"br\"></span>";

              }
            }
        ?>
      </p>
      <span class="br"></span>
    </section>
  <?php endfor; ?>
</article>

<script>
  var result = <?php echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

  if(result){
    var map;
    var center            =   {lat: <?php echo $lat; ?>, lng: <?php echo $lon; ?>};
    var map_center_pin    =   'img/map_center_pin.png';
    var map_pins          =   <?php echo json_encode($map_pins       , JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var data_lat          =   <?php echo json_encode($result_10_lat  , JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var data_lon          =   <?php echo json_encode($result_10_lon  , JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var result_10_name    =   <?php echo json_encode($result_10_name , JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    /*map表示*/
    function initMap() {
      map = new google.maps.Map(document.getElementById('map'),
      {
      center: center,
      zoom  : 13
      });

      /*marker(現在地)*/
      marker = new google.maps.Marker(
        {
        position: center,
        map     : map,
        icon    : map_center_pin,
        });

      /*marker(検索結果)*/
      for (var i = 0; i < data_lat.length; i++){
        var markerLatLon = new google.maps.LatLng(parseFloat(data_lat[i]),parseFloat(data_lon[i]));
        marker[i]        = new google.maps.Marker(
          {
          position: markerLatLon,
          map     : map,
          icon    : map_pins[i],
          });
        markerInfo(marker[i],String(result_10_name[i]['0']),i);
      }
    }

    /*marker吹き出し*/
    function markerInfo(marker_info, name,number) {
      new google.maps.InfoWindow({
        content: "<a href =\'#" +number+ "\' data-scroll>"+name+"</a>"
      }).open(marker_info.getMap(), marker_info);
    }
  }
</script>

<script src = "https://maps.googleapis.com/maps/api/js?<your api key>&callback=initMap" async defer></script>

<script>
	smoothScroll.init() ;
</script>
</body>
</html>
