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
small_industry.id,
(
			   6371 * acos (
				 cos ( radians('$latit') )
				 * cos( radians( ST_Y(ST_CENTROID(geom)) ) )
				 * cos( radians( ST_X(ST_CENTROID(geom)) ) - radians('$longi') )
				 + sin ( radians('$latit') )
				 * sin( radians( ST_Y(ST_CENTROID(geom)) ) )
			   )
			 ) AS jarak,
small_industry.name,
industry_gallery.gallery_industry as ik_gallery,
ST_X(ST_CENTROID(small_industry.geom)) AS lng,
ST_Y(ST_CENTROID(small_industry.geom)) AS lat
FROM
small_industry
JOIN
industry_gallery ON small_industry.id = industry_gallery.id
	JOIN
detail_product_small_industry ON small_industry.id = detail_product_small_industry.id_small_industry
WHERE
detail_product_small_industry.id_product IN ($c)
HAVING jarak <= $rad
LIMIT 3";

$hasil=mysqli_query($conn, $querysearch);
while($row = mysqli_fetch_array($hasil))
	{
		$id=$row['id'];
		$name=$row['name'];
		//$name=$row['name'];
		$longitude=$row['lng'];
		$latitude=$row['lat'];
		if(($row['ik_gallery']=='-')||($row['ik_gallery']=='')){
			$foto="foto.jpg";
		  }
		  else{
			$foto=$row['ik_gallery'];
		  }

		$dataarray[]=array('id'=>$id,'name'=>$name,'longitude'=>$longitude,'latitude'=>$latitude,'foto'=>$foto);
	}
echo json_encode ($dataarray);
?>
