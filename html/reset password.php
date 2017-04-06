https://www.digitalocean.com/community/tutorials/how-to-set-up-password-authentication-with-apache-on-ubuntu-14-04   

 $username = $_POST['user'];
    $password = $_POST['pass'];
    $new_username = $_POST['newuser'];
    $new_password = $_POST['newpass'];
    $action = $_POST['action'];
    //read the file into an array
    $lines = explode("\n", file_get_contents('.htpasswd'));

    //read the array and change the data if found
    $new_file = "";
    foreach($lines as $line)
    {
        $line = preg_replace('/\s+/','',$line); // remove spaces
        if ($line) {
            list($user, $pass) = split(":", $line, 2);
            if ($user == $username) {
                if ($action == "password") {
                    $new_file .= $user.':'.$new_password."\n";
                } else {
                    $new_file .= $new_username.':'.$pass."\n";
                }
            } else {
                $new_file .= $user.':'.$pass."\n";
            }
        }
    }

    //save the information
    $f=fopen(".htpasswd","w") or die("couldn't open the file");
    fwrite($f,$new_file);
    fclose($f);

// Password to be encrypted for a .htpasswd file
$clearTextPassword = 'some password';

// Encrypt password
$password = crypt($clearTextPassword, base64_encode($clearTextPassword));

// Print encrypted password
echo $password;