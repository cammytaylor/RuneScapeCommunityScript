<?php
session_start();
require('../includes/config.php');
require('../structure/database.php');
require('../structure/base.php');
require('../structure/user.php');
require('../structure/user.register.php');

$database = new database($db_host, $db_name, $db_user, $db_password);
$base = new base($database);
$user = new user($database);
$register = new user_register($database);

//preform basic checks before loading page
if($user->isLoggedIn()) $base->redirect('../index.php');

//lets check if they already have three accounts (max # of accs per ip)
$database->processQuery("SELECT * FROM `users` WHERE `ip` = ?", array($_SERVER['REMOTE_ADDR']), false);

//0: no error   1: error
$err = ($database->getRowCount() >= 3) ? 2 : 0;
    
if(isset($_POST['age']) && isset($_POST['country']))
{
    if(!in_array($_POST['age'], array('Below 13', '13-18', '19-24', '25-30', '31-36', '36-39', '40+')) || !ctype_digit($_POST['country']))
    {
        $err = 1;
    }
    else
    {
        $_SESSION['age'] = $_POST['age'];
        $_SESSION['country'] = $_POST['country'];
        $base->redirect('username.php');
    }
}
else
{
    $register->clear();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="Expires" content="0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta name="MSSmartTagsPreventParsing" content="TRUE">
<link rel="shortcut icon" href="../img/favicon.ico" />
<title><?php echo $data['wb_title']; ?></title>
<link href="../css/basic-3.css" rel="stylesheet" type="text/css" media="all">
<link href="../css/register-1.css" rel="stylesheet" type="text/css" media="all">
<?php include('../includes/google_analytics.html'); ?>
<style type="text/css">
#emailSection, #ageSection, #usernameSection, #termsSection, #passwordSection {
        visibility: hidden;
}
</style>
<script type="text/javascript">
function goBack()
{
	window.history.back();
}	
</script>
</head>

<body>
<div id="body">
<div style="text-align: center; background: none;">
        <div class="titleframe e">
                <b>Create a free account (New user)</b><br>
                <a href="../index.php">Main Menu</a>
        </div>
</div>
<br>

<div id="reg">
        <table cellspacing="0" cellpadding="0">
                <tbody><tr>
                        <td id="ageReg" class="cur">&nbsp; Age and Location &nbsp;</td>
			<td id="usernameReg" class="box">&nbsp; Choose a Username &nbsp;</td>
			<td id="termsReg" class="box">&nbsp; Terms and Conditions &nbsp;</td>
			<td id="passwordReg" class="box">&nbsp; Choose a Password &nbsp;</td>
			<td id="finishReg" class="box">&nbsp; Finish &nbsp;</td>
                </tr>
        </tbody></table>
</div>

<div class="frame wide_e">
<table width="100%">
<tbody>
<tr>
<td style="text-align: justify; vertical-align: top;">
<?php
    if($err >= 1)
    {
        switch($err)
        {
            case 1:
                echo 'Invalid age or country. <input type="button" value="Back" onclick="goBack()" />';
                break;
            case 2:
                echo 'You\'re only allowed three accounts. <input type="button" value="Back" onclick="goBack()" />';
                break;
            default:
                echo 'Undefined error.';
                break;
        }
    }
    else
    {
        ?>
    
            <form action="index.php" method="post">
            <table width="100%" cellpadding="5">
            <tbody><tr>
            <td align="center">
            <br>Please indicate your age:<br><br>

            <select name="age">
            <option value="-1" selected="selected" disabled="disabled">Select one</option>
            <option value="Below 13">Below 13</option>
            <option value="13-18">13-18</option>
            <option value="19-24">19-24</option>
            <option value="25-30">25-30</option>
            <option value="31-36">31-36</option>
            <option value="36-39">36-39</option>
            <option value="40+">Above 40</option>
            </select>
            <br>
            </td>
            </tr>
            <tr>
            <td align="center">
            Please select your country of residence:<br><br>

            <select id="country" name="country">
            <option selected="selected" disabled="disabled">Select one</option>
            <option label="--------------">
            <option value="225">United States</option>
            <option value="77">United Kingdom</option>
            <option label="--------------">

            <option value="5">Afghanistan</option>
            <option value="8">Albania</option>
            <option value="61">Algeria</option>
            <option value="14">American Samoa</option>
            <option value="3">Andorra</option>
            <option value="11">Angola</option>
            <option value="7">Anguilla</option>
            <option value="12">Antarctica</option>

            <option value="6">Antigua and Barbuda</option>
            <option value="13">Argentina</option>
            <option value="9">Armenia</option>
            <option value="17">Aruba</option>
            <option value="16">Australia</option>
            <option value="15">Austria</option>
            <option value="18">Azerbaijan</option>
            <option value="32">Bahamas</option>
            <option value="25">Bahrain</option>

            <option value="21">Bangladesh</option>
            <option value="20">Barbados</option>
            <option value="36">Belarus</option>
            <option value="22">Belgium</option>
            <option value="37">Belize</option>
            <option value="27">Benin</option>
            <option value="28">Bermuda</option>
            <option value="33">Bhutan</option>
            <option value="30">Bolivia</option>

            <option value="19">Bosnia and Herzegovina</option>
            <option value="35">Botswana</option>
            <option value="34">Bouvet Island</option>
            <option value="31">Brazil</option>
            <option value="104">British Indian Ocean Territory</option>
            <option value="29">Brunei Darussalam</option>
            <option value="24">Bulgaria</option>
            <option value="23">Burkina Faso</option>
            <option value="26">Burundi</option>

            <option value="114">Cambodia</option>
            <option value="47">Cameroon</option>
            <option value="38">Canada</option>
            <option value="52">Cape Verde</option>
            <option value="121">Cayman Islands</option>
            <option value="41">Central African Republic</option>
            <option value="207">Chad</option>
            <option value="46">Chile</option>
            <option value="48">China</option>

            <option value="53">Christmas Island</option>
            <option value="39">Cocos (Keeling) Islands</option>
            <option value="49">Colombia</option>
            <option value="116">Comoros</option>
            <option value="42">Congo</option>
            <option value="40">Congo, The Democratic Republic of the</option>
            <option value="45">Cook Islands</option>
            <option value="50">Costa Rica</option>

            <option value="97">Croatia</option>
            <option value="51">Cuba</option>
            <option value="54">Cyprus</option>
            <option value="55">Czech Republic</option>
            <option value="58">Denmark</option>
            <option value="57">Djibouti</option>
            <option value="59">Dominica</option>
            <option value="60">Dominican Republic</option>
            <option value="216">East Timor</option>

            <option value="62">Ecuador</option>
            <option value="64">Egypt</option>
            <option value="203">El Salvador</option>
            <option value="87">Equatorial Guinea</option>
            <option value="66">Eritrea</option>
            <option value="63">Estonia</option>
            <option value="68">Ethiopia</option>
            <option value="71">Falkland Islands (Malvinas)</option>
            <option value="73">Faroe Islands</option>

            <option value="70">Fiji</option>
            <option value="69">Finland</option>
            <option value="74">France</option>
            <option value="75">France, Metropolitan</option>
            <option value="80">French Guiana</option>
            <option value="170">French Polynesia</option>
            <option value="208">French Southern Territories</option>
            <option value="76">Gabon</option>
            <option value="84">Gambia</option>

            <option value="79">Georgia</option>
            <option value="56">Germany</option>
            <option value="81">Ghana</option>
            <option value="82">Gibraltar</option>
            <option value="88">Greece</option>
            <option value="83">Greenland</option>
            <option value="78">Grenada</option>
            <option value="86">Guadeloupe</option>
            <option value="91">Guam</option>

            <option value="90">Guatemala</option>
            <option value="85">Guinea</option>
            <option value="92">Guinea-Bissau</option>
            <option value="93">Guyana</option>
            <option value="98">Haiti</option>
            <option value="95">Heard Island and McDonald Islands</option>
            <option value="228">Holy See (Vatican City State)</option>
            <option value="96">Honduras</option>
            <option value="94">Hong Kong</option>

            <option value="99">Hungary</option>
            <option value="107">Iceland</option>
            <option value="103">India</option>
            <option value="100">Indonesia</option>
            <option value="106">Iran, Islamic Republic of</option>
            <option value="105">Iraq</option>
            <option value="101">Ireland</option>
            <option value="102">Israel</option>
            <option value="108">Italy</option>

            <option value="109">Jamaica</option>
            <option value="111">Japan</option>
            <option value="110">Jordan</option>
            <option value="122">Kazakstan</option>
            <option value="112">Kenya</option>
            <option value="115">Kiribati</option>
            <option value="119">Korea, Republic of</option>
            <option value="120">Kuwait</option>

            <option value="113">Kyrgyzstan</option>
            <option value="132">Latvia</option>
            <option value="124">Lebanon</option>
            <option value="129">Lesotho</option>
            <option value="128">Liberia</option>
            <option value="133">Libyan Arab Jamahiriya</option>
            <option value="126">Liechtenstein</option>
            <option value="130">Lithuania</option>

            <option value="131">Luxembourg</option>
            <option value="143">Macau</option>
            <option value="139">Macedonia, the Former Yugoslav Republic of</option>
            <option value="137">Madagascar</option>
            <option value="151">Malawi</option>
            <option value="153">Malaysia</option>
            <option value="150">Maldives</option>
            <option value="140">Mali</option>
            <option value="148">Malta</option>

            <option value="138">Marshall Islands</option>
            <option value="145">Martinique</option>
            <option value="146">Mauritania</option>
            <option value="149">Mauritius</option>
            <option value="238">Mayotte</option>
            <option value="152">Mexico</option>
            <option value="72">Micronesia, Federated States of</option>
            <option value="136">Moldova, Republic of</option>
            <option value="135">Monaco</option>

            <option value="142">Mongolia</option>
            <option value="242">Montenegro</option>
            <option value="147">Montserrat</option>
            <option value="134">Morocco</option>
            <option value="154">Mozambique</option>
            <option value="141">Myanmar</option>
            <option value="155">Namibia</option>
            <option value="164">Nauru</option>
            <option value="163">Nepal</option>

            <option value="161">Netherlands</option>
            <option value="10">Netherlands Antilles</option>
            <option value="156">New Caledonia</option>
            <option value="166">New Zealand</option>
            <option value="160">Nicaragua</option>
            <option value="157">Niger</option>
            <option value="159">Nigeria</option>
            <option value="165">Niue</option>
            <option value="158">Norfolk Island</option>

            <option value="144">Northern Mariana Islands</option>
            <option value="162">Norway</option>
            <option value="167">Oman</option>
            <option value="173">Pakistan</option>
            <option value="180">Palau</option>
            <option value="178">Palestinian Territory, Occupied</option>
            <option value="168">Panama</option>
            <option value="171">Papua New Guinea</option>
            <option value="181">Paraguay</option>

            <option value="169">Peru</option>
            <option value="172">Philippines</option>
            <option value="176">Pitcairn</option>
            <option value="174">Poland</option>
            <option value="179">Portugal</option>
            <option value="177">Puerto Rico</option>
            <option value="182">Qatar</option>
            <option value="183">Reunion</option>
            <option value="184">Romania</option>

            <option value="185">Russian Federation</option>
            <option value="186">Rwanda</option>
            <option value="193">Saint Helena</option>
            <option value="117">Saint Kitts and Nevis</option>
            <option value="125">Saint Lucia</option>
            <option value="175">Saint Pierre and Miquelon</option>
            <option value="229">Saint Vincent and the Grenadines</option>
            <option value="236">Samoa</option>
            <option value="198">San Marino</option>

            <option value="202">Sao Tome and Principe</option>
            <option value="187">Saudi Arabia</option>
            <option value="199">Senegal</option>
            <option value="239">Serbia</option>
            <option value="189">Seychelles</option>
            <option value="197">Sierra Leone</option>
            <option value="192">Singapore</option>
            <option value="196">Slovakia</option>
            <option value="194">Slovenia</option>

            <option value="188">Solomon Islands</option>
            <option value="200">Somalia</option>
            <option value="240">South Africa</option>
            <option value="89">South Georgia and the South Sandwich Islands</option>
            <option value="67">Spain</option>
            <option value="127">Sri Lanka</option>
            <option value="190">Sudan</option>
            <option value="201">Suriname</option>
            <option value="195">Svalbard and Jan Mayen</option>

            <option value="205">Swaziland</option>
            <option value="191">Sweden</option>
            <option value="43">Switzerland</option>
            <option value="204">Syrian Arab Republic</option>
            <option value="220">Taiwan</option>
            <option value="211">Tajikistan</option>
            <option value="221">Tanzania, United Republic of</option>
            <option value="210">Thailand</option>
            <option value="209">Togo</option>

            <option value="212">Tokelau</option>
            <option value="215">Tonga</option>
            <option value="218">Trinidad and Tobago</option>
            <option value="214">Tunisia</option>
            <option value="217">Turkey</option>
            <option value="213">Turkmenistan</option>
            <option value="206">Turks and Caicos Islands</option>
            <option value="219">Tuvalu</option>
            <option value="223">Uganda</option>

            <option value="222">Ukraine</option>
            <option value="4">United Arab Emirates</option>
            <option value="77">United Kingdom</option>
            <option value="225">United States</option>
            <option value="224">United States Minor Outlying Islands</option>
            <option value="226">Uruguay</option>
            <option value="227">Uzbekistan</option>
            <option value="234">Vanuatu</option>
            <option value="230">Venezuela</option>

            <option value="233">Vietnam</option>
            <option value="231">Virgin Islands, British</option>
            <option value="232">Virgin Islands, U.S.</option>
            <option value="235">Wallis and Futuna</option>
            <option value="65">Western Sahara</option>
            <option value="237">Yemen</option>
            <option value="241">Zambia</option>
            <option value="243">Zimbabwe</option>
            </optgroup>
            </select>
            <br>

            </td>
            </tr>
            <tr>
            <td align="center">
            <input type="submit" value="Continue">
            </td>
            </tr>
            </tbody></table>
            </div></form>

            </form>
    
        <?php
    }
?>
</td>
</tr>
</tbody>
</table>
</div>
</div>

<div class="tandc"><?php echo $data['wb_foot']; ?></div>	
</body></html>