<?php
include_once("lib/_db.php");
//----------------------------Insert character reference----------------------------
$table='characters';
$params['id']=12;
$params['name']='Hoof-Boo';
$params['bio']='';
$params['display']=1;
insert_query($table,$params);

unset($params);
//----------------------------Insert character images----------------------------
//laughing
$table='character_images';

$params['character_id']=12;
$params['name']='laughing';
$params['asset']='/assets/characters/hoof_boo_laughing.jpg';
insert_query('character_images',$params);
//full-wink2

$params['character_id']=12;
$params['name']='full-wink';
$params['asset']='/assets/characters/hoof_boo_full_wink.jpg';
insert_query('character_images',$params);


//mad
$params['character_id']=12;
$params['name']='mad';
$params['asset']='/assets/characters/hoof_boo_mad.jpg';
insert_query('character_images',$params);

//huh
$params['character_id']=12;
$params['name']='huh';
$params['asset']='/assets/characters/hoof_boo_huh.jpg';
insert_query('character_images',$params);

//full-oops

$params['character_id']=12;
$params['name']='full-oops';
$params['asset']='/assets/characters/hoof_boo_full_oops.jpg';
insert_query('character_images',$params);

//oops

$params['character_id']=12;
$params['name']='oops';
$params['asset']='/assets/characters/hoof_boo_oops.jpg';
insert_query('character_images',$params);

//wink
$params['character_id']=12;
$params['name']='wink';
$params['asset']='/assets/characters/hoof_boo_wink.jpg';
insert_query('character_images',$params);

//wink2
$params['character_id']=12;
$params['name']='wink2';
$params['asset']='/assets/characters/hoof_boo_wink2.jpg';
insert_query('character_images',$params);

//neutral
$params['character_id']=12;
$params['name']='neutral';
$params['asset']='/assets/characters/hoof_boo_neutral.jpg';
insert_query('character_images',$params);

//full-neutral
$params['character_id']=12;
$params['name']='full-neutral';
$params['asset']='/assets/characters/hoof_boo_full_neutral.jpg';
insert_query('character_images',$params);

//full-sad
$params['character_id']=12;
$params['name']='full-sad';
$params['asset']='/assets/characters/hoof_boo_full_sad.jpg';
insert_query('character_images',$params);
exec_query($sql);

//full-mad2
$params['character_id']=12;
$params['name']='full-mad';
$params['asset']='/assets/characters/hoof_boo_full_mad.jpg';
insert_query('character_images',$params);


//afraid
$params['character_id']=12;
$params['name']='afraid';
$params['asset']='/assets/characters/hoof_boo_afraid.jpg';
insert_query('character_images',$params);

//full-afraid
$params['character_id']=12;
$params['name']='full-afraid';
$params['asset']='/assets/characters/hoof_boo_full_afraid.jpg';
insert_query('character_images',$params);

//full-huh
$params['character_id']=12;
$params['name']='full-huh';
$params['asset']='/assets/characters/hoof_boo_full_huh.jpg';
insert_query('character_images',$params);

//full-laugh
$params['character_id']=12;
$params['name']='full-laugh';
$params['asset']='/assets/characters/hoof_boo_full_laugh.jpg';
insert_query('character_images',$params);


echo "character image inserted";
?>
