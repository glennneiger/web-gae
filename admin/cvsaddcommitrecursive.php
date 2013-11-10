<?php
 
function getDirectory( $path = '.', $level = 0 ){
 
    // Directories to ignore when listing output. Many hosts
    // will deny PHP access to the cgi-bin.
    $ignore = array( 'cgi-bin', '.', '..','CVS' );
 
 // Open the directory to the handle $dh
    $dh = opendir( $path );
 
    // Loop through the directory
    while( false !== ( $file = readdir( $dh ) ) ){
 
  // Check that this file is not to be ignored
        if( !in_array( $file, $ignore ) and !ereg("#.*",$file)){
 
            $spaces = str_repeat( '&nbsp;', ( $level * 4 ) );
            // Just to add spacing to the list, to better
            // show the directory tree.
 
   // Its a directory, so we need to keep reading down...
            if( is_dir( "$path/$file" ) ){
 
                echo "<strong>$spaces $file</strong><br />";
    shell_exec("cvs add \"".$path."/".$file."\"");
    //echo shell_exec("cvs commit -m \"\"");
                getDirectory( "$path/$file", ($level+1) );
                // Re-call this same function but on a new directory.
                // this is what makes function recursive.
 
            } else {
 
            #    echo "$spaces $file<br />";
           #     $cmd= "cvs add -kb \"".$path."/".$file."\"";
             #   echo $cmd;
 
                shell_exec("cvs add -kb \"".$path."/".$file."\"");
    shell_exec("cvs commit -m \"\" \"".$path."/".$file."\"");
                // Just print out the filename
 
            }
 
        }
 
    }
 
    closedir( $dh );
    // Close the directory handle
 
}
function cvsupdate($dir)
{
	shell_exec("cvs -q update -d -P -C -A '". $dir."'");
}
cvsupdate('../assets/Image');
cvsupdate('../assets/File');
cvsupdate('../assets/bios');
cvsupdate('../assets/characters');
getDirectory('../assets/Image');
getDirectory('../assets/File');
getDirectory('../assets/bios');
getDirectory('../assets/characters');
?>
