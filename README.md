# Little quiz

Friend is in college, studying Computer Science. His proffesor from "Programming Classes" gives a little quizes 24 hours before  big tests.

So I tried one quiz and succeeded in less than 2 hours. Yey!

# Text of the quiz
There is _7z_ archive packed using password.

The starting password was: 

	6DWHPPmVJ2X4WayJFxmAGJf8

But, to not things be too easy, the password was hashed/encrypted/encoded by these five functions:

* SHA1
* ROT13
* BASE64
* MD5
* SHA3 (Keccak) (256bit) 

It was encoded five times, but you need to guess an order and which functions were used. Some of them might not be used at all. Nothing is sure.

5 functions, random order, random usage - so we need permutations with repeated items.

# What to do?
As I counted, its only 3905 possibilities, so we can hack this using brute-force.

First, lets write function which checks if password is correct:

> function checkPassword($password, $file) {
>    exec('7z e -y -p'.$password.' '.$file, $output);
>    if(!strstr($output[6], 'Wrong password')) {
>        return true;
>    }
>    return false;
> }

It will run _7z_ program via shell, and check if we don't have _Wrong password_ message on line 6. I tested it with my own packed _archive.7z_ (I have used password **AAA**, you can check it).

Okey, then, lets find some usefull code for our permutations, I found it on Google

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

It just works so I won't go into details.

We have all available permutations in an array now which looks like this:

    base64_encode sha1 str_rot13 md5 sha3
    str_rot13 base64_encode sha1 md5 sha3
    base64_encode str_rot13 sha1 md5 sha3
    sha1 str_rot13 md5 base64_encode sha3
    str_rot13 sha1 md5 base64_encode sha3

So, we need helper function which will encode our starting password with provided functions:

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

As you can see, for **SHA3** I used special parameter to say that I need only 256bit password. (as quiz said)

Then, we just need to put everything into **foreach** statement!


    $file = './Kolokwium2013.7z';
    $password = '6DWHPPmVJ2X4WayJFxmAGJf8';

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

And after 20 minutes on my MacBook I got that permutation ** 2234** was correct one and functions used were 

	base64_encode str_rot13 md5 sha1 sha3

As you can easily check, correct password is:

	b017eae9a42ec71defb4ee1aa4d4866ca142dd8eb1c4abf8ba8ea6d26df61596
