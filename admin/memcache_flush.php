<?
// code to clear memcache
$memcache = new memCacheObj();
$memcache->deleteKey("qpOpenPositiontrade");  // delete memcache of open position trade 
$memcache->deleteKey("qpOpenPosition");   // delete memcache of open position 
$memcache->deleteKey("qpDisplayPerformance"); // delete memcache of display performance
?>
