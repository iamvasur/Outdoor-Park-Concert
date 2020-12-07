<?php


    //
    // WARNING:
    // to be safe, you should remove your username and password before commiting to git
    // to implement a application "for real", students should learn and study OAUTH
    //
    $servername = "192.168.2.27";
    $username = "admin";
    $password = "1234";
    $databaseName = "concert";

    //
    // open connection
    //
    $conn = new mysqli($servername, $username, $password, $databaseName);

    if ($conn->connect_error) {
        die("ERROR: Connection failed: " . $conn->connect_error);
    }


    /*
     * read conert db
     */
    $css = file_get_contents('./index.css');
    $path = "./";
    $fileName = "concertSociallyDistanced.json";
    $jsonFile = fopen($path . $fileName, "r") or die("Unable to open file!");

    // get json string from file
    $jsonData = file_get_contents($fileName);

    // decode json into array, returning fully-associative array (ordered map, hash table/hash map)
    $arrData = json_decode($jsonData, true);

    $seats = array();
    for( $i = 0;$i < 20; $i++){
        for( $j = 0;$j < 26; $j++){
            $seats[$i][$j] = 0;
        }
    }

    for ($a = 0; $a < count($arrData); $a++) {
        $firstName = $arrData[$a]["firstName"];
        $lastName = $arrData[$a]["lastName"];
        $phone = $arrData[$a]["phone"];
        $email = $arrData[$a]["email"];
        $userName = strtolower($arrData[$a]["userName"]);

        $purchaseList = $arrData[$a]["purchaseList"];
        for ($p = 0; $p < count($purchaseList); $p++) {
            $seatRow = $purchaseList[$p]["row"];
            $seatColumn = $purchaseList[$p]["column"];
            $seats[$seatRow][$seatColumn] = 1;
        }
    }

    // update the database tables
    // printf("<pre>");

    // query data in table
    $sql = "SELECT id, userName, seatRow FROM purchase ORDER BY userName;";
    $result = $conn->query($sql);

    // if ($result->num_rows > 0) {
    //     loop through the data returned from mysql db
    //     while ($row = $result->fetch_assoc()) {
    //         var_dump($row);
    //     }

    // } else {
    //     printf("ERROR: No results");
    // }

    // close connection
    //
    mysqli_close($conn);



?>

<html>
    <head>
        <title>Booking System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src = "./index.js"></script>
        <link rel = "stylesheet" href = "./index.css" type = "text/css"/>
        <link rel="stylesheet" href="index.css" type="text/css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <style type = "text/css">
    <?php
        echo $css
    ?>
    </style>
    </head>
    <body>
        <div class = "bodyContainer"> 
                <!-- <h1 style = "margin-top:50px;text-align:center">Ticket Booking</h3> -->
                <div class="mainContainer">
                    <div class="widgets">
                        <div class="widget-item" id = "search">
                            <i class="fa fa-search fa-2x" aria-hidden="true"></i><br>
                            <label>Search</label>
                        </div>

                        <div class="widget-item" id = "view_total">
                            <i class="fa fa-check fa-2x" aria-hidden="true"></i><br>
                            <label>View Total</label>
                        </div>

                        <div class="widget-item" id = "quit">
                            <i class="fa fa-close fa-2x"></i><br>
                            <label>Quit</label>
                        </div>
                    </div>
                    <div class="seatsAndOrder">
                        <div id = "seatsContainer" class="seatsContainer">
                        <?php
                            for($i = 0;$i<20;$i++){
                                print "<div class = 'seatRow' >";
                                for($j = 0;$j<26;$j++){
                                    if($seats[$i][$j] == 1){
                                        print "<div class = 'seatItem present'>X</div>";
                                    }else{
                                        print "<div class = 'seatItem absent'>a</div>";
                                    }
                                }
                                print "</div>";
                            }
                        ?>
                        </div>

                        <div class="orderContainer">
                            <input type = "text" id = "rownumber" placeholder = "Row"/>
                            <input type = "text" id = "seatsqty" placeholder = "Seats"/>
                            <button class = "orderButton">Order</button>
                        </div>
                    
                    </div>
                </div>                

                <div class = "orderModalContainer" id = "orderModalContainer">
                    <div id="orderModal">
                        <div class = "closeordermodal" id = "closeordermodal">X</div>
                        <input type = "text" id = "order_user_name" placeholder = "Enter Your User Name" /><br>
                        <input type = "text" id = "order_first_name" placeholder = "Enter Your First Name" /><br>
                        <input type = "text" id = "order_last_name" placeholder = "Enter Your Last Name" /><br>
                        <input type = "text" id = "order_phone" placeholder = "Enter Your Phone"/>
                        <input type = "text" id = "order_email" placeholder = "Enter Your Email"/><br>
                        <button id = "order_submit">Submit</button>
                    </div>
                </div> 
                </div>
                
                <div class = "searchModalContainer" id = "searchModalContainer">
                    <div id="searchModal">
                        <div class = "closeordermodal" id = "closesearchmodal">X</div>
                        <input type = "text"id = "search_user_name" placeholder = "Enter the User Name"><br>
                            <?php
                            if($_GET['search_keyword']!=""){
                                echo '<table id = "search_table">';
                                echo '</table>';
                                echo '<script type = "text/javascript">document.getElementById("searchModalContainer").className="dim"</script>';
                                echo "<script type = 'text/javascript'>document.getElementById('search_user_name').value='", $_GET['search_keyword'] ,"'</script>";

                                echo '<div class = "search_table_wrapper">';
                                echo '<table id = "search_table">';
                                echo '<tr><th>Name</th><th>Seat Row</th><th>Seat Col</th></tr>';


                                $conn2 = new mysqli($servername, $username, $password, $databaseName);

                                if ($conn2->connect_error) {
                                    die("ERROR: Connection failed: " . $conn2->connect_error);
                                }
                                $searchsql = "SELECT userName, seatRow, seatColumn FROM purchase WHERE userName = '" .  $_GET['search_keyword'] . "'";
                                $searchresult = $conn2->query($searchsql);
                                if ($searchresult->num_rows > 0) {
                                    while ($row = $searchresult->fetch_assoc()) {
                                        echo "<tr><td>", $row["userName"], "</td><td>",$row["seatRow"] , "</td>", "<td>",$row["seatColumn"], "</td></tr>";
                                    }
                                }
                                // if ($result->num_rows > 0) {
                                //     while ($row = $result->fetch_assoc()) {
                                //         $i = $i+1;
                                //         if($prevu!=$row["userName"] && $prevu!=""){ //next user
                                //         $stotal = $stotal + $utotal;
                                //         echo '<tr><td>', $prevu, '</td><td>$' ,$utotal ,'</td></tr>';
                                //         $utotal = calculateRowPrice($row["seatRow"]);
                                //         } else{
                                //             $seatrow = $row["seatRow"];
                                //             $utotal = $utotal + calculateRowPrice($row["seatRow"]);
                                //         }
                                //         $prevu = $row["userName"];

                                //         if($i == $result->num_rows){
                                //             $stotal = $stotal + $utotal;
                                //             echo '<tr><td>', $prevu, '</td><td>$' ,$utotal ,'</td></tr>';
                                //         }
                                //      }
                                // }
                                echo '</table>';
                                echo '</div>';
                                }

                             ?>
                        <button  id = "search_submit">Search</button> 
                    </div>
                </div> 
                </div>




                <div class = "totalModalContainer" id = "totalModalContainer">
                    <div id="totalModal">
                        <div class = "closeordermodal" id = "closetotalmodal">X</div>
                            <?php
                                echo '<div class = "total_table_wrapper">';
                                echo '<table id = "total_table">';
                                echo '<tr><th>Name</th><th>Income</th></tr>';
                                $utotal = 0;
                                $stotal = 0 ;
                                $rescopy = $result;
                                $i = 0;
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $i = $i+1;
                                        if($prevu!=$row["userName"] && $prevu!=""){ //next user
                                        $stotal = $stotal + $utotal;
                                        echo '<tr><td>', $prevu, '</td><td>$' ,$utotal ,'</td></tr>';
                                        $utotal = calculateRowPrice($row["seatRow"]);
                                        } else{
                                            $seatrow = $row["seatRow"];
                                            $utotal = $utotal + calculateRowPrice($row["seatRow"]);
                                        }
                                        $prevu = $row["userName"];

                                        if($i == $result->num_rows){
                                            $stotal = $stotal + $utotal;
                                            echo '<tr><td>', $prevu, '</td><td>$' ,$utotal ,'</td></tr>';
                                        }
                                     }
                                }
                                echo '</table>';
                                echo '</div>';
                                echo "Total: $" , $stotal;


                                function calculateRowPrice(int $r){
                                    $r = $r-1;
                                    if($r >=0 && $r <=4){
                                        return 80;
                                    }
                                    else if($r>=5 && $r<=10){
                                        return 50;
                                    }
                                    else{
                                        return 25;
                                    }
                                }
                             ?>
                    </div>
                </div> 
                </div>


        <script type = "text/javascript">
            <?php
                if($_GET['type'] == "order"){
                    $row=$_GET['row'];
                    $numberofseats=$_GET['numberofseats'];
                    $selectedseatrow = $seats[$row];
                    $selectedrow = -1;
                    $selectedcolumn = -1;
              
                    for($i = 0;$i<26;$i++){
                        $seatstatus = 1;
                        for($j = 0;$j<$numberofseats;$j++){
                            if($i == 0 || $i == 1){
                                if($seats[$row][$i + $j] == 1){
                                    $seatstatus = 0;
                                }
                                if($seats[$row][$i + $numberofseats +1] == 1 || $seats[$row][$i + $numberofseats + 2] == 1 ){
                                    $seatstatus = 0;
                                }
                            }
                            else if($i >1 && $i < 24){
                                if($seats[$row][$i + $j] == 1){
                                    $seatstatus = 0;
                                }
                                if($seats[$row][$i + $numberofseats] == 1 || $seats[$row][$i + $numberofseats + 1] == 1 ){
                                    $seatstatus = 0;
                                }
                                if($seats[$row][$i -1] == 1 || $seats[$row][$i - 2] == 1 ){
                                    $seatstatus = 0;
                                }
                            }
                            else if($i == 24 || $i == 25){
                                if($seats[$row][$i + $j] == 1){
                                    $seatstatus = 0;
                                }
                                if($seats[$row][$i -1] == 1 || $seats[$row][$i - 2] == 1 ){
                                    $seatstatus = 0;
                                }
                            }
                        }
                        if($seatstatus == 1){
                            // $selectedrow = $i;
                            $selectedcolumn = $i;
                            break;
                        }
                    }
                    echo "document.location.href = '/?type=orderfinal&finalrow=", $row, "&finalcolumn=", $selectedcolumn, "&finalnumberofseats=", $numberofseats, "'";
                }


                if($_GET['type'] == "orderfinal"){
                    echo "document.getElementById('orderModalContainer').className = 'dim'\n";

                    echo "document.getElementById('order_submit').onclick = function(){";
                    echo "document.location.href = '/?type=placeorder&finalrow=", $_GET['finalrow'], "&finalcolumn=", $_GET['finalcolumn'], "&finalnumberofseats=", $_GET['finalnumberofseats'], "&username='+document.getElementById('order_user_name').value+'&firstname='+document.getElementById('order_first_name').value+'&lastname='+document.getElementById('order_last_name').value+'&phone='+document.getElementById('order_phone').value+'&email='+document.getElementById('order_email').value";
                    echo "}";

                }
                if($_GET['type'] == "placeorder"){
                    $rownumber = $_GET['finalrow'];
                    $startcolumn = $_GET['finalcolumn'];
                    $numberofseats = $_GET['finalnumberofseats'];
                    $usernmae = $_GET['username'];
                    $firstname = $_GET['firstname'];
                    $lastname = $_GET['lastname'];
                    $phone = $_GET['phone'];
                    $email = $_GET['email'];

                    $conn3 = new mysqli($servername, $username, $password, $databaseName);

                    if ($conn3->connect_error) {
                        die("ERROR: Connection failed: " . $conn3->connect_error);
                    }
                    $searchresult = $conn3->query($searchsql);
                }
            ?>

        </script>

        <script>
                document.getElementsByClassName('orderButton')[0].onclick = function(){
                    var rownumber = +(document.getElementById('rownumber').value);
                    var numberofseats = +(document.getElementById('seatsqty').value);
                    if(rownumber == 0 || rownumber < 0 || rownumber > 20){
                        alert("Row number value is invalid and you must input a value between 1 and 20");
                        return; //just in case that if it doesn't reload.
                    }
                    if(numberofseats == 0){
                        alert("Number of Seats entered is invalid");
                        return;
                    }
                    rownumber-=1;
                    document.location.href = "/?type=order&row="+rownumber+"&numberofseats="+numberofseats;

                }

                document.getElementById('search').onclick = function(){
                    document.getElementById('searchModalContainer').className = "dim"
                }

                document.getElementById('view_total').onclick = function(){
                    document.getElementById('totalModalContainer').className = "dim"
                }


                document.getElementById('closeordermodal').onclick = function(){
                    document.location.href = "/";
                }
                document.getElementById('closetotalmodal').onclick = function(){
                    document.getElementById('totalModalContainer').className = "totalModalContainer"
                }

                document.getElementById('closesearchmodal').onclick = function(){
                    document.location.href = "/";
                }


                document.getElementById('quit').onclick = function(){
                    document.location.href = "/"
                }

                document.getElementById('search_submit').onclick = function(){
                    document.location.href = "/?search_keyword="+document.getElementById("search_user_name").value;
                }
                
        </script>

    </body>

</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    
</body>
</html>