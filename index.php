<?php

$file = './Kolokwium2013.7z';
$password = '6DWHPPmVJ2X4WayJFxmAGJf8';

/**
* Five algoritms used to hash above passwords
* SHA-1
* Rot13
* Base64
* MD5
* SHA-3 256bit (Keccak)
 */


/**
 * SHA3 function is not in PHP, I compiled this and added from https://github.com/strawbrary/php-sha3
 */
$hashingFunctions = array('sha1', 'str_rot13', 'base64_encode', 'md5', 'sha3');


echo 'Let\'s start'."\n";
$permutations = get_all_permutations($hashingFunctions);
echo 'Available combinations: '.count($permutations).''."\n";
$i = 0;
foreach($permutations AS $funcs) {
    $hashedPass = hashPassword($funcs, $password);
    if(checkPassword($hashedPass, $file)) {
        echo $i.': Correct password: '.$hashedPass."\n";
        echo 'Functions used in this order: '.$permutations[$i]."\n";
        exit;
    } else {
        echo $i.': Checked "'.$hashedPass.'"'."\n";
    }
    $i++;
}

/**
 * This functions hash our password by every hash function we provide.
 * For SHA3 I added another parameter because we need 256 bit hashing
 * @param $funcs Function sepaated which will hash password
 * @param $password Password which should be hashed by functions
 * @return Hashed password
 */
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

/**
 * This runs 7z script by shell and checks if file can be extracted using password
 * @param $password
 * @param $file
 * @return bool
 */
function checkPassword($password, $file) {
    exec('7z e -y -p'.$password.' '.$file, $output);
    if(!strstr($output[6], 'Wrong password')) {
        return true;
    }
    return false;
}


/**
 * Functions below I got from Google, they will produce every permutation with repeatable items.
 */
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