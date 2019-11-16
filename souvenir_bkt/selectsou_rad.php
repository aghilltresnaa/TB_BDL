<?php
require '../connect.php';

$latit=$_GET["lat"];
$longi=$_GET["lng"];
$rad=$_GET["rad"]/1000;

$lay=$_GET['lay'];
$lay = explode(",", $lay);
$c = "";
for($i=0;$i<count($lay);$i++){
	if($i == count($lay)-1){
		$c .= "'".$lay[$i]."'";
	}else{
		$c .= "'".$lay[$i]."',";
	}
}
$querysearch="SELECT 
souvenir.id,
(
			   6371 * acos (
				 cos ( radians('$latit') )
				 * cos( radians( ST_Y(ST_CENTROID(geom)) ) )
				 * cos( radians( ST_X(ST_CENTROID(geom)) ) - radians('$longi') )
				 + sin ( radians('$latit') )
				 * sin( radians( ST_Y(ST_CENTROID(geom)) ) )
			   )
			 ) AS jarak,
souvenir.name,
souvenir_gallery.gallery_souvenir as sou_gallery,
ST_X(ST_CENTROID(souvenir.geom)) AS lng,
ST_Y(ST_CENTROID(souvenir.geom)) AS lat
FROM
souvenir
JOIN
souvenir_gallery ON souvenir.id = souvenir_gallery.id
	JOIN
detail_product_souvenir ON souvenir.id = detail_product_souvenir.id_souvenir
WHERE
detail_product_souvenir.id_product IN ($c)
GROUP BY id
HAVING jarak <= $rad
ORDER BY jarak ASC
LIMIT 3";
$hasil=mysqli_query($conn, $querysearch);
while($row = mysqli_fetch_array($hasil))
	{
		$id=$row['id'];
		$name=$row['name'];
		//$name=$row['name'];
		$longitude=$row['lng'];
		$latitude=$row['lat'];
		if(($row['sou_gallery']=='-')||($row['sou_gallery']=='')){
			$foto="foto.jpg";
		  }
		  else{
			$foto=$row['sou_gallery'];
		  }

		$dataarray[]=array('id'=>$id,'name'=>$name,'longitude'=>$longitude,'latitude'=>$latitude,'foto'=>$foto);
	}
echo json_encode ($dataarray);
?>
