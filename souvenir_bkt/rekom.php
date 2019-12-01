<?php

include '../connect.php';

$querysearch ="select id, name, st_x(st_centroid(geom)) as lng, st_y(st_centroid(geom)) as lat
from souvenir where id in ('SO001','SO002','SO003','SO004')";
               
$hasil=mysqli_query($conn, $querysearch);
while($baris = mysqli_fetch_array($hasil))
    {
        $id=$baris['id'];
        $name=$baris['name'];
        
        $longitude=$baris['lng'];
        $latitude=$baris['lat'];

        $dataarray[]=array('id'=>$id,'name'=>$name,'lng'=>$longitude,'lat'=>$latitude);
    }

echo json_encode ($dataarray);

?>
