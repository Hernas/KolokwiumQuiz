<?php
$file = '~/Developer/Private/KolosKodowanie/Kolokwium2013.7z';
$password = '6DWHPPmVJ2X4WayJFxmAGJf8';

/**
 * Ale żeby nie było tak łatwo – używamy go niebezpośrednio. Hasło zostało poddane haszowaniu dokładnie 5razy z użyciem następujących algorytmów:
SHA-1
Rot13
Base64
MD5
SHA-3 256bit (Keccak)
 */

$hashingFunctions = array('sha1', 'str_rot13', 'base64_encode', 'md5', 'sha3');
echo 'Let\'s start'."\n";
$words = array('a','b','c');
$permutations = get_all_permutations($hashingFunctions);
echo 'Available combinations: '.count($permutations).''."\n";
$i = 0;
echo $permutations[2234];
exit;
foreach($permutations AS $funcs) {
    $hashedPass = hashPassword($funcs, $password);
    if(checkPassword($hashedPass, $file)) {
        echo 'Correct password: '.$hashedPass."\n";
        exit;
    } else {
        echo $i.': Checked "'.$hashedPass.'"'."\n";
    }
    $i++;
}
function hashPassword($funcs, $password) {
    $ff = explode(' ', $funcs);
    foreach($ff AS $function) {
        if($function == 'sha3') {
            $password = call_user_func($function, $password, 256);
        } else {
            $password = call_user_func($function, $password);
        }
    }
    return $password;
}

function checkPassword($password, $file) {
    exec('7z e -y -p'.$password.' '.$file, $output);
    if(!strstr($output[6], 'Wrong password')) {
        return true;
    }
    return false;
}

function permutations($arr,$n)
{
    $res = array();
    foreach ($arr as $w)
    {
        if ($n==1) $res[] = $w;
        else
        {
            $perms = permutations($arr,$n-1);
            foreach ($perms as $p)
            {
                $res[] = $w." ".$p;
            }
        }
    }
    return $res;
}

function get_all_permutations($words=array())
{
    $r = array();
    for($i=sizeof($words);$i>0;$i--)
    {
        $r = array_merge(permutations($words,$i),$r);
    }
    return $r;
}
/**
 * 7z e archive.7z U

7-Zip [64] 9.20  Copyright (c) 1999-2010 Igor Pavlov  2010-11-18
p7zip Version 9.20 (locale=utf8,Utf16=on,HugeFiles=on,4 CPUs)

Processing archive: archive.7z

Extracting  UnicornVLDB-final.pdf     Data Error in encrypted file. Wrong password?

Sub items Errors: 1
 */

/**
 * 7-Zip [64] 9.20  Copyright (c) 1999-2010 Igor Pavlov  2010-11-18
p7zip Version 9.20 (locale=utf8,Utf16=on,HugeFiles=on,4 CPUs)

Processing archive: archive.7z

Extracting  UnicornVLDB-final.pdf

Everything is Ok

Size:       751582
Compressed: 631107
 */