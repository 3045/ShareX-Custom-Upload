<?php
header('Content-type:application/json;charset=utf-8');
error_reporting(E_ERROR);

// Replace REPLACE_ME1 with a randomly generated string. Adding more tokens for other users is as simple as adding it within quotes after a comma.
$tokens = array("REPLACE_ME1");

// File types that can be uploaded to prevent unauthorized skids from causing damage. Add and remove as you see fit.
$filetypes = array('webm','webp','png','jpg','jpeg','tif','tiff','gif','svg','bmp','mp3','mp4','wav','ogg','flac','wma','mpa','cda','zip','7z','7zip','rar','sql');

$sharexdir = "i/";   // File directory - where uploads are stored relative to the domain (domain.tld/i/) leave blank for root directory.
$lengthofstring = 7; // File name length - different lengths do increase or decrease the chances of someone bruteforcing your images.

// You should not need to edit anything below this line, if you do please exercise caution.

// Common default token values as SHA256 hashes. Ideally this needs to be done better but 'it works'.
$defaults = array(
    "d53f9c0e95bd6b3aab57a7e3a8f6085e971f32475a782d406ea4474cde7e9943",
    "df3e6b0bb66ceaadca4f84cbc371fd66e04d20fe51fc414da8d1b84d31d178de",
    "d8cc7aed3851ac3338fcc15df3b6807b89125837f77a75b9ecb13ed2afe3b49f",
    "8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92",
    "3c469e9d6c5875d37a43f353d4f88e61fcf812c66eee3457465a40b0da4153e0",
    "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918",
    "3be404bfeafe49285f4035bccd1840c1972a202e1a7664ffc196284b97a99797",
    "5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8",
    "5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5",
    "65e84be33532fb784c48129675f9eff3a682b27168c0ea744b2cf58ee02337c5",
    "15e2b0d3c33891ebb0f1ef609ec419420c20e320ce94c65fbc8c3312448eb225"
);
// Random file name generation
function RandomString($length) {
    $keys = array_merge(range(0,9), range('a', 'z'));
 
    for($i=0; $i < $length; $i++) {
        $key .= $keys[mt_rand(0, count($keys) - 1)];
    }
    return $key;
}
 
// Check if the token is set and that it's not default.
if(isset($_POST['secret'] && !in_array(hash('sha256', $_POST['secret']),$defaults)) { 
    //Checks if token is valid
    if(in_array($_POST['secret'], $tokens)) {
        $target_file = $_FILES["sharex"]["name"];
        $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if(in_array($fileType, $filetypes)) {
            
            //random string for the file name
            $filename = RandomString($lengthofstring);
        
            //Accepts and moves to directory
            if (move_uploaded_file($_FILES["sharex"]["tmp_name"], $sharexdir.$filename.'.'.$fileType)) {
                //Sends info to client
                $json = ['status' => 'OK','errormsg' => '','url' => $filename . '.' . $fileType];
            } else {
               //Warning
               $json = ['status' => 'ERROR','errormsg' => '','url' => "File upload failed - does the folder $sharexdir exist with correct permissions?"];
            }
        } else {
            $json = ['status' => 'ERROR','errormsg' => '','url' => 'File upload failed - invalid file type.'];
        }
    } else {
        //Invalid key
        $json = ['status' => 'ERROR','errormsg' => '','url' => 'File upload failed - invalid secret key.'];
    }
} else {
    //Warning if no uploaded data
    $json = ['status' => 'ERROR','errormsg' => '','url' => 'Please stop using the default token. You can generate a secure, randomized token using an online password generator.'];
}
//Sends json
echo(json_encode($json));
?>
