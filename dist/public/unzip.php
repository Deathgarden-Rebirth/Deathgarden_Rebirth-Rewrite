<?php
$source = '../storage/vendor.zip';
$dest = '../vendor';

// $source2 = '../storage/vendor_public.zip';
// $dest2 = '../public/assets/vendor';

// Start measure
$starttime = microtime(true);

// Delete existing directory
// print "Removing existing directory...<br>";
// rrmdir($dest2);

// print "Extracting archive...<br>";

// $zip = new ZipArchive;
// Zip File Name
// if ($zip->open($source2) === TRUE) {
    // Unzip Path
//    $zip->extractTo($dest2);
//    $zip->close();
//    print 'Unzipped Process Successful!<br>';
// } else {
//    print 'Unzipped Process failed<br>';
// }

// Delete existing directory
print "Removing existing directory...<br>";
rrmdir($dest);

print "Extracting archive...<br>";

$zip = new ZipArchive;
// Zip File Name
if ($zip->open($source) === TRUE) {
    // Unzip Path
    $zip->extractTo($dest);
    $zip->close();
    print 'Unzipped Process Successful!<br>';
} else {
    print 'Unzipped Process failed<br>';
}

$endtime = microtime(true);
print "Executing this script took ".$endtime-$starttime." seconds!";

function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir")
           rrmdir($dir."/".$object);
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
    print $dir."<br>";
  }
}
