<!doctype html>

<html class="no-js" lang="en"><script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<?php require_once("../../../Connections/db.php"); ?>

<?php
$gre = mysql_query("SELECT * FROM setting") or die(mysql_error());
$dat = mysql_fetch_array($gre);

$sit = $dat['sitename'];

 define("sitename",$sit);?>

<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "verified,unverified";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../../../account";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$currentPage = $_SERVER["PHP_SELF"];

$colname_access = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_access = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbconnect, $dbconnect);
$query_access = sprintf("SELECT * FROM users WHERE email = %s", GetSQLValueString($colname_access, "text"));
$access = mysql_query($query_access, $dbconnect) or die(mysql_error());
$row_access = mysql_fetch_assoc($access);
$totalRows_access = mysql_num_rows($access);

$maxRows_products = 10;
$pageNum_products = 0;
if (isset($_GET['pageNum_products'])) {
  $pageNum_products = $_GET['pageNum_products'];
}
$startRow_products = $pageNum_products * $maxRows_products;

mysql_select_db($database_dbconnect, $dbconnect);
$query_products = "SELECT * FROM products ORDER BY serial DESC";
$query_limit_products = sprintf("%s LIMIT %d, %d", $query_products, $startRow_products, $maxRows_products);
$products = mysql_query($query_limit_products, $dbconnect) or die(mysql_error());
$row_products = mysql_fetch_assoc($products);

if (isset($_GET['totalRows_products'])) {
  $totalRows_products = $_GET['totalRows_products'];
} else {
  $all_products = mysql_query($query_products);
  $totalRows_products = mysql_num_rows($all_products);
}
$totalPages_products = ceil($totalRows_products/$maxRows_products)-1;


$queryString_products = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_products") == false && 
        stristr($param, "totalRows_products") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_products = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_products = sprintf("&totalRows_products=%d%s", $totalRows_products, $queryString_products);
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo sitename?> | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../../assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="../../assets/css/typography.css">
    <link rel="stylesheet" href="../../assets/css/default-css.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="../../assets/js/vendor/modernizr-2.8.3.min.js"></script>
    
<link rel="stylesheet" href="../../../assets/css/stockmenu.css">

<link rel="stylesheet" href="../../../assets/css/switch.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloaderr">
        <div class="loaderr"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <!-- sidebar menu area start -->
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                   
					
                <a href="#"><img src="../../assets/images/icon/logo.png" alt="logo"></a>    
                </div>
                  
                               
                
               
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                    
                        <ul class="metismenu" id="menu">
                           
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user-plus"></i><span>My Account
                                    </span></a>
                                <ul class="collapse">
                                    <li><a href="../../profile">Edit Profile</a></li>
                                    <li><a href="../../profile/security">Security</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-history"></i><span>Transactions</span></a>
                                <ul class="collapse">
                                    <li><a href="../../history">History</a></li>
                                    
                                </ul>
                            </li>
                            
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-area-chart"></i><span>MarketPlace</span></a>
                                <ul class="collapse">
                                    <li><a href="../../../dashboard">Forex</a></li>
                                    <li><a href="../stock">Stock</a></li>
                                    <li><a href="../crypto">Cryptocurrency</a></li>
                                    <li><a href="../indices">Indices</a></li>
                                    <li><a href="../cfd">Oil & Gas</a></li>
                                    
                                </ul>
                            </li>
                            
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-money"></i><span>Deposit</span></a>
                                <ul class="collapse">
                                    <li><a href="#" data-toggle="modal" data-target="#exampleModalCenter">New Request</a></li>
                                      <li><a href="../../deposit">History</a></li>
                                  
                                </ul>
                            </li>
                            
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-bank"></i><span>Withdrawal</span></a>
                                <ul class="collapse">
                                    <li><a href="../../withdrawal">New Request</a></li>
                                      <li><a href="../../withdrawal/history">History</a></li>
                                  
                                </ul>
                            </li>
                           
                           
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-power-off"></i> <span>Logout</span></a>
                                <ul class="collapse">
                                    <li><a href="../../../logout">Logout</a></li>
                                    
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <!-- sidebar menu area end -->
        <!-- main content area start -->
        <div class="main-content">
            
            
            
                                <!-- Button trigger modal -->
                               
                              
            
            
            
            <!-- header area start -->
          
              
            <!-- header area end -->
            <!-- page title area start -->
          
            
                  <div class="top">
  
   <?php 
											$email = $row_access['email'];
											$user = $row_access['firstname'];
											$id  = rand(58754,95782);
											$date = date("d/m/Y");
											$stat = "Pending";
											
											$min = "250";
											$action = "Add Fund";
											
											if(isset($_POST['depo'])){
											
											$amount = $_POST['amount'];
											$method = $_POST['method'];
											
											if($amount >= $min){
											
										$inst = mysql_query("INSERT INTO deposit(orderno,user,email,amount,method,status,date,action) VALUE('$id','$user','$email','$amount','$method','$stat','$date','$action')") or die(mysql_error());	
										
										if($inst){
											
											echo '<div class="alert alert-success">
											<strong>Order Received</strong> Our representative will get in touch with you shortly with necessary information regarding your deposit. 
											</div>';
											}
											}else{ echo '<div class="alert alert-danger">
											<strong>Amount Too Low</strong>. Minimum deposit is $250.00
											</div>';}
											
											}
											
											?> 
                                            
                                            
                                            
                                            <?php 
											// submit buy order
											
											$email = $row_access['email'];
											$user = $row_access['firstname'];
											$blc = $row_access['bal'];
											
											$id  = rand(58754,95782);
											$date = date("h:i:sA");
											$stat = "Pending";
											
											$min = "250";
											$action = "Execute";
											
											if(isset($_POST['buy']) or isset($_POST['sell'])){
											
											$amount = $_POST['amount'];
											$type = $_POST['type'];
											$ordertype = $_POST['ordertype'];
											$symbol = $_POST['symbol'];
											$sl = $_POST['sl'];
											$tp = $_POST['tp'];
											$remark = $_POST['comment'];
											
											if($amount <= $blc){
											
										$inst = mysql_query("INSERT INTO trade(id,symbol,amount,type,remark,time,email,status,action,SL,TP) VALUE('$id','$symbol','$amount','$ordertype','$remark','$date','$email','$stat','$action','$sl','$tp')") or die(mysql_error());	
										
										if($inst){
											
											echo '<div class="alert alert-success">
											<strong> Order has been placed in queue</strong>  <span>#'.$id.' market '.$ordertype.'  '.$row_access['currency'].''.number_format($amount,2,'.',',').' <strong>'.$symbol.'</strong> Done </span> 
											</div>';
											
											
											}
											}else{ echo '<div class="alert alert-danger">
											<strong>Low Balance</strong>. Fund your account
											</div>';}
											
											}
											
											?> 
                                            
  
  <ul class="menu">
    
    <li><a href="../../../dashboard" >Forex</a></li>
    <li><a href="../stock" >Stock</a></li>
    <li><a href="../crypto" >Cryptocurrency</a></li>
    <li><a href="../indices" >Indices</a></li>
    <li><a href="../cfd" class="selected">Oil & Gas</a></li>
    
    <li><button class="btn btn-danger" data-toggle="modal" data-target="#exampleModalLong">Sell</button></li>
   
   <li ><button class="btn btn-success" data-toggle="modal" data-target=".bd-example-modal-lg">Buy</button></li>
    
  
   <li> 
   
  
   
   <!-- Rounded switch -->
   
<label class="switch">
  <input  <?php 
  
  $usr = $row_access['email'];
  $tpos = $row_access['auto'];
  $aut = "off";
  
  
  if($tpos !== $aut) {echo "checked=\"checked\""; echo "disabled=\"disabled\"";} else{echo "check=\"check\""; }
  
   ?> name="switch" type="checkbox" id="switch" onClick=" " >
  <span class="slider round"></span>
</label>


 
  
   </li>
   
    <li><button class="btn btn-success" data-toggle="modal" data-target="#exampleModalCenter">Deposit</button></li>
      <li> <!-- profile info & task notification -->
                   
                        <ul class="notification-area pull-right">
                        
                         <div class="user-profile pull-right">
                            
                                  <h5><?php echo $row_access['currency'];?> <?php echo number_format($row_access['bal'],2,'.',',');?></h5>
                                
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                                
                                <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onClick="withd()">Withdrawal</a>
                                <a class="dropdown-item" href="#" onClick="history()">History</a>
                                <a class="dropdown-item" href="#" onClick="trans()">Transactions</a>
                                <a class="dropdown-item" href="#" onClick="logout()">Log Out</a>
                            </div>
                        </div>
                        
                            
                            
                            <li class="dropdown">
                               
                            </li>
                            <li class="dropdown">
                                
                            </li>
                           
                        </ul>
                    </li>
  </ul>
</div>
           
            
<!-- TradingView Widget BEGIN -->

  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
  <script type="text/javascript">
  new TradingView.widget(
  {
  "width": "auto",
  "height": 610,
  "symbol": "OANDA:BCOUSD",
  "timezone": "Etc/UTC",
  "theme": "Dark",
  "style": "1",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "withdateranges": true,
  "range": "all",
  "allow_symbol_change": true,
  "save_image": false,
  "details": true,
  "hotlist": true,
  "calendar": true,
  "news": [
    "stocktwits",
    "headlines"
  ],
  "studies": [
    "BB@tv-basicstudies",
    "MACD@tv-basicstudies",
    "MF@tv-basicstudies"
  ],
  "container_id": "tradingview_f263f"
}
  );
  </script>

<!-- TradingView Widget END -->



            
             
                       						<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the text: " + copyText.value);
}
</script>
<script>
function myFunction2() {
  var copyText = document.getElementById("myInput2");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  alert("Copied the text: " + copyText.value);
}
</script>
                                <div class="modal fade" id="exampleModalCenter">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Deposit Fund</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                            
                                                <p>                                               
                                                <div class="form-group">
						
                                                    <label for="example-text-input" class="col-form-label">Please Copy the Address </label>
                                        <label for="example-text-input" class"col-form-label">And send the amount you want to invest.</label></label>            
                                            <h7><br><p style="color:#FF0000";>Bitcoin </p><input class="float-center" type="text" value="1GqP6k3RncL8UvhpVb8wjeCniSS42SoWxJ"  size="40" id="myInput" readonly>
<button class="btn btn-danger" onclick="return myFunction()"> Copy Wallet </button></h7>
											<center><img class="img-responsive" style="margin: auto" src="https://chart.googleapis.com/chart?chs=200x200&amp;cht=qr&amp;chl=1GqP6k3RncL8UvhpVb8wjeCniSS42SoWxJ"></center>
											
											<h7><br> <p style="color:#FF0000";>Ethereum </p><input class="float-center" type="text" value="0xA2C0B6eC090d6312F23b5a3F4Ee68F470eAC2F9a"  size="40"  id="myInput2" readonly>
<button class="btn btn-danger" onclick="myFunction2()"> Copy Wallet </button></h7>
<center><img class="img-responsive" style="margin: auto" src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=Ethereum:0xA2C0B6eC090d6312F23b5a3F4Ee68F470eAC2F9a"></center>
                                        </div>
										 <label for="example-text-input" class"col-form-label">Investment range from 150 USD to 50000 USD</label></label>  
                                         
                                                 </p>
                                            </div>
                                            <div class="modal-footer" id="response">
																	<script>
            $(document).ready(function(){
                $("#clkMe").click(function(){
                    var dataString={};
                    $.ajax({                                      
                        url:"../../read-deposits.php",
                        type: 'POST',
                        cache:false,
                        data: dataString,
                        beforeSend: function() {},
                        timeout:10000,
                        error: function() { },     
                        success: function(response) {
                           $("#response").html(response);
                           alert("Analyzing ... If your deposit has been made, it will be credited to your account.");
                        } 
                    });
                });
            });
        </script>
                                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                              <button type="button" id="clkMe"  name="depo" class="btn btn-flat btn-success btn-lg btn-block" >Deposited</button>    									 
                                           </div>
                                        </div>
                                    </div>
                                    
                                </div>
                           
                                          
                                         
                                                 </p>
                                            </div>
                                            <div class="modal-footer">
                                        
                                           </div></form>
                                        </div>
                                    </div>
                                    
                                </div>
                    
            
                    
                   <?php 
								$email = $row_access['email']; 
							$sql = mysql_query("SELECT * FROM trade WHERE email='$email' ORDER BY time DESC limit 5") or die(mysql_error());
							
							$num = mysql_num_rows($sql);
							
							if($num > 0 ){
								
								}
							$data = mysql_fetch_assoc($sql);		
								 
								 ?>
                                 
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                    <th scope="col">Order</th>
                                                    <th scope="col">Time</th>
                                                    <th scope="col">Type</th>
                                                    
                                                    <th scope="col">Symbol</th>
                                                    <th scope="col">Volume</th>
                                                    <th scope="col">S/L</th>
                                                    <th scope="col">T/P</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-uppercase bg-dark">
                                                <tr class="text-white">
                                                <?php do { ?>
                                                    <th scope="row"><?php echo $data['id'];?></th>
                                                    <td><?php echo $data['time'];?></td>
                                                    <td><?php echo $data['type'];?></td>
                                                    <td><?php echo $data['symbol'];?></td>
                                                    <td><?php echo $data['amount'];?></td>
                                                    <td><?php echo $data['SL'];?></td>
                                                    <td><?php echo $data['TP'];?></td>
                                                    <td><?php echo $data['status'];?></td>
                                                    
                                                </tr>
                                                
                                            <?php } while($data = mysql_fetch_assoc($sql)); ?>    
                                                
                                            </tbody>
                                        </table>   
                        
                           
                                 
                <!-- sales report area end -->
                <!-- overview area start -->
               
                    </div>
                   
                </div>
                <!-- overview area end -->
                <!-- market value area start -->
               
                <!-- market value area end -->
                <!-- row area start -->
              
                <!-- row area end -->
               
                <!-- row area start-->
            </div>
        </div>
        <!-- main content area end -->
        
           <!-- Large modal -->
                               
                                <div class="modal fade bd-example-modal-lg">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                              <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                  <div class="row">
                    <div class="col-md-5 pr-md-1">
                      <div class="form-group">
                        <label>Volume</label>
                        <input type="number" class="form-control"  name="amount" placeholder="Amount to buy" value="" step="any" required>
                      </div>
                    </div>
                    <div class="col-md-3 px-md-1">
                      <div class="form-group">
                        <label>Type</label>
                        <select  class="form-control" name="type" >
                        <option value="Market Execution">Market Execution</option>
                        <option value="Pending Order">Pending Order</option>
                        </select>
                        <input type="hidden" name="ordertype" value="buy" >
                      </div>
                    </div>
                    <div class="col-md-4 pl-md-1">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Symbol</label>
                        <select name="symbol" class="form-control" >
                        <option value="GBPUSD">GBPUSD, Great Britain Pound vs US Dollar</option>
                        <option value="CHFJPY">CHFJPY,Swiss Franc vs Japanise Yen </option>
                        <option value="EURAUD">EURAUD, Euro vs Australian Dollar</option>
                        <option value="EURCHF">EURCHF, Euro vs Swiss Franc</option>
                        <option value="EURUSD">EURUSD, Euro vs US Dollar</option>
                        <option value="EURGBP">EURGBP, Euro vs Great Britain </option>
                        <option value="GBPCAD">GBPCAD, Great Britain Pound vs Canadian Dollar</option>
                        <option value="GBPNZD">GBPNZD, Great Britain Pound vs New Zealand Dollar</option>
                        <option value="NZDJPY">NZDJPY, New Zealand vs Japanise Yen</option>
                        <option value="NZDUSD">NZDUSD, New Zealand vs US Dollar</option>
                        <option value="NZDCHF">NZDCHF, New Zealand vs Swiss France</option>
                        <option value="EURONZD">EURNZD, Euro vs New Zealand </option>
                        <option value="CADJPYm">CADJPYm, Canadian Dollar vs Japanise Yen</option>
                        <option value="AUDJPYm">AUDJPYm, Australian Dollar vs Japanise Yen</option>
                        <option value="USDCHF">USDCHF, US Dollar vs Swiss Franc</option>
                        <option value="GBPAUD">GBPAUD, Great Britain Pound vs Australian Dollar</option>
                        
                        <option value="USDTRY">USDTRY, US Dollar vs Turkish New Lira</option>
                        
                        <option value="USDTHB">USD vs THB</option>
                        
                        <option value="AUDUSD">AUD vs USD</option>
                        
                        <option value="CADJPY">CAD vs JPY</option>
                        
                        <option value="USDRUB">USDRUB, US Dollar vs Russian Ruble</option>
                        
                        <option value="EURCHF">EURCHF, Euro vs Swiss France</option>
                        
                        <option value="NZDCHF">NZDCHF, New Zealand Dollar vs Swiss France</option>
                        
                        <option value="XAUUSD">XAUUSD, Gold vs US Dollar</option>
                        <option value="NZDJPY">NZDJPY, New Zealand Dollar vs Japanise Yen</option>
                        
                        <option value="CADJPY">CAD vs JPY</option>
                        
                        
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 pr-md-1">
                      <div class="form-group">
                        <label>Stop Loss</label>
                        <input type="number" name="sl" class="form-control"  min="0" value="0.0000" step="any"  required>
                      </div>
                    </div>
                    <div class="col-md-6 pl-md-1">
                      <div class="form-group">
                        <label>Take Profit</label>
                        <input type="number" name="tp" class="form-control"  min="0" value="0.0000" step="any" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Comment</label>
                        <input type="text"  class="form-control" name="comment"  >
                      </div>
                    </div>
                  </div>
                  
                    
                    
                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label></label>
                        <input type="submit" name="buy" class="btn btn-block btn-success btn-lg" value="Buy by Market">
                      </div>
                    </div>
                  </div>
                </form>      
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Large modal modal end -->
        <!-- footer area start-->
     
        <!-- footer area end-->
    </div>
    <!-- page container area end -->
    
    
    <!-- Button trigger modal -->
                               
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModalLong">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                 <form method="post" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                  <div class="row">
                    <div class="col-md-5 pr-md-1">
                      <div class="form-group">
                        <label>Volume</label>
                        <input type="number" class="form-control"  name="amount" placeholder="Amount to sell" value="" step="any" required>
                      </div>
                    </div>
                    <div class="col-md-3 px-md-1">
                      <div class="form-group">
                        <label>Type</label>
                        <select  class="form-control" name="type" >
                        <option value="Market Execution">Market Execution</option>
                        <option value="Pending Order">Pending Order</option>
                        </select>
                        <input type="hidden" name="ordertype" value="sell" >
                      </div>
                    </div>
                    <div class="col-md-4 pl-md-1">
                      <div class="form-group">
                        <label for="exampleInputEmail1">Symbol</label>
                        <select name="symbol" class="form-control" >
                         <option value="GBPUSD">GBPUSD, Great Britain Pound vs US Dollar</option>
                        <option value="CHFJPY">CHFJPY,Swiss Franc vs Japanise Yen </option>
                        <option value="EURAUD">EURAUD, Euro vs Australian Dollar</option>
                         <option value="EURUSD">EURUSD, Euro vs US Dollar</option>
                        <option value="EURCHF">EURCHF, Euro vs Swiss Franc</option>
                        <option value="EURGBP">EURGBP, Euro vs Great Britain </option>
                        <option value="GBPCAD">GBPCAD, Great Britain Pound vs Canadian Dollar</option>
                        <option value="GBPNZD">GBPNZD, Great Britain Pound vs New Zealand Dollar</option>
                        <option value="NZDJPY">NZDJPY, New Zealand vs Japanise Yen</option>
                        <option value="NZDUSD">NZDUSD, New Zealand vs US Dollar</option>
                        <option value="NZDCHF">NZDCHF, New Zealand vs Swiss France</option>
                        <option value="EURONZD">EURNZD, Euro vs New Zealand </option>
                        <option value="CADJPYm">CADJPYm, Canadian Dollar vs Japanise Yen</option>
                        <option value="AUDJPYm">AUDJPYm, Australian Dollar vs Japanise Yen</option>
                        <option value="USDCHF">USDCHF, US Dollar vs Swiss Franc</option>
                        <option value="GBPAUD">GBPAUD, Great Britain Pound vs Australian Dollar</option>
                        
                        <option value="USDTRY">USDTRY, US Dollar vs Turkish New Lira</option>
                        
                        <option value="USDTHB">USD vs THB</option>
                        
                        <option value="AUDUSD">AUD vs USD</option>
                        
                        <option value="CADJPY">CAD vs JPY</option>
                        
                        <option value="USDRUB">USDRUB, US Dollar vs Russian Ruble</option>
                        
                        <option value="EURCHF">EURCHF, Euro vs Swiss France</option>
                        
                        <option value="NZDCHF">NZDCHF, New Zealand Dollar vs Swiss France</option>
                        
                        <option value="XAUUSD">XAUUSD, Gold vs US Dollar</option>
                        <option value="NZDJPY">NZDJPY, New Zealand Dollar vs Japanise Yen</option>
                        
                        <option value="CADJPY">CAD vs JPY</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 pr-md-1">
                      <div class="form-group">
                        <label>Stop Loss</label>
                        <input type="number" name="sl" class="form-control"  min="0" value="0.0000" step="any" required>
                      </div>
                    </div>
                    <div class="col-md-6 pl-md-1">
                      <div class="form-group">
                        <label>Take Profit</label>
                        <input type="number" name="tp" class="form-control"  min="0" value="0.0000"  step="any" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label>Comment</label>
                        <input type="text"  class="form-control" name="comment"  >
                      </div>
                    </div>
                  </div>
                  
                    
                    
                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label></label>
                        <input type="submit" name="sell" class="btn btn-block btn-danger btn-lg" value="Sell by Market">
                      </div>
                    </div>
                  </div>
                </form> 
                                                
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Sell Modal End -->
    <!-- offset area start -->
    <div class="offset-area">
        <div class="offset-close"><i class="ti-close"></i></div>
        <ul class="nav offset-menu-tab">
            <li><a class="active" data-toggle="tab" href="#activity">Activity</a></li>
            <li><a data-toggle="tab" href="#settings">Settings</a></li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <div class="recent-activity">
                    <div class="timeline-task">
                        <div class="icon bg1">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-check"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Added</h4>
                            <span class="time"><i class="ti-time"></i>7 Minutes Ago</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>You missed you Password!</h4>
                            <span class="time"><i class="ti-time"></i>09:20 Am</span>
                        </div>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="fa fa-bomb"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Member waiting for you Attention</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="ti-signal"></i>
                        </div>
                        <div class="tm-title">
                            <h4>You Added Kaji Patha few minutes ago</h4>
                            <span class="time"><i class="ti-time"></i>01 minutes ago</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg1">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Ratul Hamba sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Hello sir , where are you, i am egerly waiting for you.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg2">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="fa fa-bomb"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                    <div class="timeline-task">
                        <div class="icon bg3">
                            <i class="ti-signal"></i>
                        </div>
                        <div class="tm-title">
                            <h4>Rashed sent you an email</h4>
                            <span class="time"><i class="ti-time"></i>09:35</span>
                        </div>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse distinctio itaque at.
                        </p>
                    </div>
                </div>
            </div>
            <div id="settings" class="tab-pane fade">
                <div class="offset-settings">
                    <h4>General Settings</h4>
                    <div class="settings-list">
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Notifications</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch1" />
                                    <label for="switch1">Toggle</label>
                                </div>
                            </div>
                            <p>Keep it 'On' When you want to get all the notification.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show recent activity</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch2" />
                                    <label for="switch2">Toggle</label>
                                </div>
                            </div>
                            <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show your emails</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch3" />
                                    <label for="switch3">Toggle</label>
                                </div>
                            </div>
                            <p>Show email so that easily find you.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Show Task statistics</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch4" />
                                    <label for="switch4">Toggle</label>
                                </div>
                            </div>
                            <p>The for attribute is necessary to bind our custom checkbox with the input.</p>
                        </div>
                        <div class="s-settings">
                            <div class="s-sw-title">
                                <h5>Notifications</h5>
                                <div class="s-swtich">
                                    <input type="checkbox" id="switch5" />
                                    <label for="switch5">Toggle</label>
                                </div>
                            </div>
                            <p>Use checkboxes when looking for yes or no answers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- offset area end -->
    <!-- jquery latest version -->
    <script src="../../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <!-- bootstrap 4 js -->
    <script src="../../assets/js/popper.min.js"></script>
    <script src="../../assets/js/bootstrap.min.js"></script>
    <script src="../../assets/js/owl.carousel.min.js"></script>
    <script src="../../assets/js/metisMenu.min.js"></script>
    <script src="../../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../../assets/js/jquery.slicknav.min.js"></script>

    <!-- start chart js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <!-- start highcharts js -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- start zingchart js -->
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script>
    zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
    </script>
    <!-- all line chart activation -->
    <script src="../../assets/js/line-chart.js"></script>
    <!-- all pie chart -->
    <script src="../../assets/js/pie-chart.js"></script>
    <!-- others plugins -->
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/scripts.js"></script>
    
</body>

</html>
