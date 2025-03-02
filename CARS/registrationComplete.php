<?php
// Start the session
session_start();

// Include the database connection file: include_once("config.php");
include_once("config.php");

if (
    isset($_SESSION['logged_in']) && $_SESSION['studentID']
    && $_SESSION['username']
) {
    $now = time(); // Checking the time now when page loads.

    if ($now > $_SESSION['expire']) {
        session_destroy();
        header('Location: processLogout.php?action=logout');
    }
}

//if not logged in, redirect to home page
if($_SESSION['logged_in'] == false)
header("Location: home.php?action=login");

// if there was no activity selected, go to main activity page
if(empty($_POST['category'])==true)
header("Location: myActivity.php");
?>

<!DOCTYPE html>
<html>

<head>
    <title>CARS (Register Page)</title>
    <meta charset="UTF-8">

    <!-- BookStrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- RAY's bookstrap -->
    <link rel="stylesheet" type="text/css" href="css/fontawesome-free-5.13.0-web/css/all.css">
    <link rel="stylesheet" type="text/css" href="css/home.css">

    <!-- My CSS -->
    <link rel="stylesheet" type="text/css" href="css/registerActivity.css">                
</head>

<body>
    <!-- NAVBAR -->
    <div id="navbar" class="navbar navbar-expand-sm navbar-light navbar-colored" style="padding: 5px 10px;">

        <!-- Logos (UM, KK8) -->
        <img id="logo" class="um-logo" src="imgs/UM-LOGO.png" alt="um-logo" width="120" height="45" style="font-size: 25px;">
        <img id="logo" class="kk8-logo" src="imgs/KK8-LOGO.png" alt="kk8-logo" width="40" height="35">

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Links to other pages -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="home.php#profile">About</a>
                </li>
                <li class="nav-item">
                    <?php
                    if (
                        isset($_SESSION['logged_in']) && $_SESSION['studentID']
                        && $_SESSION['username']
                    ) {
                        echo '<a class="nav-link" href="myActivity.php">Activities</a>';
                    } else {
                        echo '<a class="nav-link" href="home.php?action=login">Activities</a>';
                    }
                    ?>
                </li>
                <li class="nav-item">
                    <?php
                    if (
                        isset($_SESSION['logged_in']) && $_SESSION['studentID']
                        && $_SESSION['username']
                    ) {
                        echo '<a class="nav-link" href="helpdesk.php">Help Desk</a>';
                    } else {
                        echo '<a class="nav-link" href="home.php?action=login">Help Desk</a>';
                    }
                    ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="home.php#team">Management</a>
                </li>
                <li class="nav-item">
                    <a id="contact-us" class="nav-link" href="#footer">Contact Us</a>
                </li>
            </ul>

            <?php
            if (
                isset($_SESSION['logged_in']) && $_SESSION['studentID']
                && $_SESSION['username']
            ) {
                echo
                    '<ul class="nav navbar-nav navbar-right ml-auto">
                            <li class="nav-item dropdown">
                                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle user-action">
                        ';

                // Method 1: Using MySQLi Procedural
                $username = $_SESSION['username'];
                $studentID = $_SESSION['studentID'];
                
                $check_profile_pic =  mysqli_query($mysqli, "SELECT profile_pic_path FROM student_T WHERE studentID = '$studentID' AND username = '$username'");
                $res = mysqli_fetch_array($check_profile_pic);
                $image = "imgs/profilepicture/".$res["profile_pic_path"];

                // Display profile picture
                echo '<img src="' . $image . '" class="avatar" alt="Avatar">';

                // Display username
                echo $_SESSION['username'];
                echo '<b class="caret"></b></a>';

                // Display dropdown menu and log out button
                echo
                    '<ul class="dropdown-menu dropdown-menu-right">
                                    <li><a href="userprofile.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a></li>
                                    <li class="divider dropdown-divider"></li>
                                    <li>
                                        <div class="text-center">
                                            <a href="processLogout.php?action=logout" class="btn btn-danger" role="button">Log Out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>';
            } else {
                echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#login-modal">Log
                        In</button>';
            } ?>
        </div>
    </div>

    <main>
        <div class="mainContainer">
            <div class="containerAct">
                <?PHP
                
                //check if student have registered or not
                $checkforReg = "SELECT * FROM registration_t WHERE studentID='".$_SESSION["studentID"]."' AND activityID='".$_SESSION['activityID']."'";
                $checkforReg = mysqli_query($mysqli,$checkforReg);
                    if(mysqli_num_rows($checkforReg)==0):
                ?>

                <h2>REGISTERING FOR:</h2>
                <?php
                    // print_r($_POST);
                    // print_r($_FILES);
                    // CHECK IF FILE IS UPLOADED, IF SO, CHECK FOR ALL REQUIREMENTS
                    $count = 0;
                    $fileError=false;
                    $fileDestination = '';
                    if(isset($_FILES['fileUploaded'])){
                        if($_FILES['fileUploaded']['size']>0){
                            // change size input limit
                            // ini_set('display_errors', '1');


                            $fileError=true;
                            //print_r( $_FILES['fileUploaded']);
                            $file = $_FILES['fileUploaded'];
                            $fileName = $file['name'];
                            $fileTmpName = $file['tmp_name'];
                            $fileSize = $file['size'];
                            $fileError = $file['error'];
                            $fileType = $file['type'];
                            $fileExt = explode('.',$fileName);
                            $actualFileExt = strtolower(end($fileExt));
                            $allowedFileType = array('zip','rar','doc','docx','txt','pdf');
                            $errorlist = [];
                            if(in_array($actualFileExt,$allowedFileType)){
                                $count++;
                            }else{
                                array_push($errorlist,'<h6 style=\'color:gray;\'>Sorry, we don\'t support "'.$actualFileExt.'" type file.</h6>');
                            }

                            if($fileError===0){
                                $count++;
                            }else{
                                array_push($errorlist,'<h6 style=\'color:gray;\'>There was an error uploading your file.</h6>');
                            }

                            if($fileSize<20000000){ //20mb
                                $count++;
                            }else{
                                array_push($errorlist,'<h6 style=\'color:gray;\'>File size cannot be more than 20mb.</h6>');
                            }

                            // UPLOAD
                            if($count==3){ //all true, size,type,no error
                                //search if there is activity dictionary exist, if no, create
                                $folderPath = "activityFiles/".$_POST['actJTK']."/".$_POST['actName'];
                                if(!file_exists($folderPath)){
                                    mkdir($folderPath,0777,true);
                                }
                                $fileDestination = $folderPath."/".$_SESSION['studentID']."_".$fileExt[0].".".$actualFileExt;
                                move_uploaded_file($fileTmpName,$fileDestination);
                                $fileError=false;
                            }else{
                                echo "<div class='backbtn' onclick='gobackA()'><img src='imgs/LogoBackgroundIcon/BackBTN2.svg' width='40px' alt=''></div>";
                                echo "<h3 class='activityTitle'>".$_POST['actName']." (".$_POST['actJTK'].")</h3><br>";

                                echo "<div class='modal-box complete'>
                                    <h1 style='color:gray;'>File Cannot Be Uploaded<br>:')</h1><br>";
                                foreach($errorlist as $error)
                                    echo $error;
                                echo    "<br>
                                    <button id='back2actpage' class='btn btn-primary' onclick='gobackA()'>Reregister</button>
                                </div>";
                                $fileError=true;
                            }
                        }
                    }

                    
                    // IF theres file error, skip all
                    if($fileError == false):
                        echo "<div class='backbtn' onclick='goback()'><img src='imgs/LogoBackgroundIcon/BackBTN2.svg' width='40px' alt=''></div>";
                        echo "<h3 class='activityTitle'>".$_POST['actName']." (".$_POST['actJTK'].")</h3>";
                ?>

                <div class="progressbar">
                    <div class="points step1 done"></div>
                    <div class="points step2 done"></div>
                    <div class="points step3 done"></div>
                    <div class="points step4 done"></div>
                </div>

                <div id="gap">
                </div>

                <?php
                    // store information in registration_t 
                    if($_POST['category'] == "Participant"){
                        $qryParticipant = "INSERT INTO registration_t(studentID,activityID,category,time_slot_picked,reg_status) 
                                            VALUES('".$_SESSION['studentID']."','".$_SESSION['activityID']."','".$_POST['category']."','".$_POST['time_slot']."','In Progress')";
                        mysqli_query($mysqli,$qryParticipant);

                    }elseif($_POST['category'] == "Volunteer"){
                        $qryVolunteer = "INSERT INTO registration_t(studentID,activityID,category,bureau_picked,reason_joining,sent_file,reg_status) 
                                            VALUES('".$_SESSION['studentID']."','".$_SESSION['activityID']."','".$_POST['category']."','".$_POST['biro_chosen']."','".$_POST["reason"]."','".$fileDestination."','In Progress')";
                        mysqli_query($mysqli,$qryVolunteer);  
                    }else{}
                ?>
                
                <!-- COMPLETED REGITSRATION BOX -->
                <div class="modal-box complete">
                    <img src="imgs/LogoBackgroundIcon/completed.gif" width="30%" alt="">
                    <h1>Registration completed!</h1>
                    <h5>Thank you for taking part in this activity</h5>
                    <br>
                    <button id="back2actpage" class="btn btn-primary" onclick="goback()">Back to activity page</button>
                </div>

                <?PHP
                    endif;
                else:
                ?>

                <!-- Registration has already done but user click refresh(prevent double registration)-->
                    <div class="modal-box complete">
                        <h1>You are already have registerd this actvity</h1>
                        <br>
                        <button id="back2actpage" class="btn btn-primary" onclick="goback()">Back to activity page</button>
                    </div>

                <?PHP
                endif;
                ?>

                <br><br><br>
            </div>
        </div>

    <?PHP
        mysqli_close($mysqli);
    ?>
    </main>

    <footer id="footer" class="footer-distributed">
        <div class="footer-left">

            <img id="logo" class="um-logo" src="imgs/UM-LOGO.png" alt="um-logo" width="240" height="90">
            <img id="logo" class="kk8-logo" src="imgs/KK8-LOGO.png" alt="kk8-logo" width="80" height="70">

            <p class="footer-links">
                <?php
                if (
                    isset($_SESSION['logged_in']) && $_SESSION['studentID']
                    && $_SESSION['username']
                ) {
                    echo
                        '
                        <a href="home.php">Home</a>
                        ·
                        <a href="home.php#profile">About</a>
                        ·
                        <a href="myActivity.php">Activities</a>
                        ·
                        <a href="helpdesk.php">Help Desk</a>
                        ·
                        <a href="home.php#team">Management</a>
                        ·
                        <a href="#footer">Contact Us</a>
                        ';
                } else {
                    echo
                        '
                        <a href="home.php">Home</a>
                        ·
                        <a href="home.php#profile">About</a>
                        ·
                        <a href="home.php?action=login">Activities</a>
                        ·
                        <a href="home.php?action=login">Help Desk</a>
                        ·
                        <a href="home.php#team">Management</a>
                        ·
                        <a href="#footer">Contact Us</a>
                        ';
                }
                ?>
            </p>

            <p class="footer-company-name">© 2020 CitizenScientist</p>
        </div>

        <div class="footer-center">
            <div>
                <i class="fa fa-map-marker"></i>
                <p><span>Kolej Kediaman Kinabalu, University of Malaya</span> 50603 Kuala Lumpur</p>
            </div>

            <div>
                <i class="fa fa-phone"></i>
                <p>03-7955 8643</p>
            </div>

            <div>
                <i class="fa fa-envelope"></i>
                <p><a href="mailto:kinabalu@um.edu.my">kinabalu@um.edu.my</a></p>
            </div>
        </div>

        <div class="footer-right">
            <p class="footer-company-about">
                <span>About KK8</span>
                Kinabalu Residential College is the 8th residential college in University of Malaya.
            </p>

            <div class="footer-icons">
                <div class="f_social_icon">
                    <span class="facebook">
                        <a href="https://www.facebook.com/Kolej-Kediaman-Kinabalu-KK8-Universiti-Malaya-OFFICIAL-148001638614165/" class="fab fa-facebook"></a>
                    </span>
                    <span class="twitter">
                        <a href="https://twitter.com/kinabalu8th?lang=en" class="fab fa-twitter"></a>
                    </span>
                    <span class="instagram">
                        <a href="https://www.instagram.com/kolej_kediaman8/?hl=en" class="fab fa-instagram"></a>
                    </span>
                    <span class="youtube">
                        <a href="https://www.youtube.com/channel/UCzZEY-tpmrvap3R-zPf7JJg" class="fab fa-youtube"></a>
                    </span>
                </div>
            </div>
        </div>

    </footer>

    <!-- BootStrap Script -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>


    <!-- My Script -->
    <script src="js/registerCompleteScript.js"></script>
</body>

</html>