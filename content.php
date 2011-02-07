<div id="tabs">
<ul>
	<li><a href="#tabs-1">Employees</a></li>
	<li><a href="#tabs-2">Sites</a></li>
	<li><a href="#tabs-3">Computers</a></li>
	<li><a href="#tabs-4">Promotional codes</a></li>
</ul>
<div id="tabs-1" >
	<table id="EmployeeTable" class="display">
		<thead>
			<tr>
				<th>ID</th>
				<th>Site</th>
				<th>CustomerID</th>
				<!--<th>ComputerID</th>-->
				<th>Name</th>
				<th>PhoneNo</th>
				<th>Comment</th>
				<th>MailAddress</th>
				<th>MailAddress2</th>
				<th>Password</th>
				<th>Optician</th>
				<th>Scientist</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (isset($link)) {
					$summat = mysql_query("SELECT * FROM EmployeeView");
					while ($row = mysql_fetch_assoc($summat)) {
						$scientist = ($row[Scientist]) ? "Yes" : "No";
						$optician = ($row[Optician]) ? "Yes" : "No";
						print "<tr>";
						print "<td id='ID_$row[ID]_Employee'>$row[ID]</td>";
						print "<td id='SiteID_$row[ID]' class='editInPlace_Employee_Site_dropdown'>" . specialchars($row[Site]) . "</td>";
						print "<td id='CustomerID_$row[ID]_Employee'>$row[EmployeeCustomerID]</td>";
						print "<td id='Name_$row[ID]_Employee' class='editInPlace_Employee'>" . specialchars($row[Name]) . "</td>";
						print "<td id='PhoneNo_$row[ID]' class='editInPlace_Employee'>" . specialchars($row[PhoneNo]) . "</td>";
						print "<td id='Comment_$row[ID]' class='editInPlace_Employee_multirow'>" . specialchars($row[Comment]) . "</td>";
						print "<td id='MailAddress_$row[ID]_Employee' class='editInPlace_Employee'>" . specialchars($row[MailAddress]) . "</td>";
						print "<td id='MailAddress2_$row[ID]_Employee' class='editInPlace_Employee'>" . specialchars($row[MailAddress2]) . "</td>";
						print "<td id='Password_$row[ID]' class='editInPlace_Employee'>$row[Password]</td>";
						print "<td id='Optician_$row[ID]' class='editInPlace_Employee_dropdown'>$optician</td>";
						print "<td id='Scientist_$row[ID]' class='editInPlace_Employee_dropdown'>$scientist</td>";
						print "<td id='Delete_Employee_$row[ID]' style='text-align:right;'><div class='Delete_icon'></div></td></tr>";
					}
				}
			?>
		</tbody>
	</table>
	<div class="ButtonPanel">
		<div id="switcher" style="float: left"></div>
		<button class="Button_OpenNew" id="Button_OpenNewEmployee" onmouseover="tooltip('Key shortcut: Alt+E');" onmouseout="exit();">New <u>E</u>mployee</button>
		<?php
			if (isset($_SESSION['password'])) {
				print "<div id='sessioncontrol' style='float: right'><button id='logout' onClick=\"location.href='logout.php'\">Logout</button></div>";
			}
		?>
	</div>
</div>

<div id="tabs-2">
	<table id="SiteTable" class="display">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Address</th>
				<th>Zip</th>
				<th>City</th>
				<th>StreetAddress</th>
				<th>StreetZip</th>
				<th>StreetCity</th>
				<th>PhoneNo</th>
				<th>FaxNo</th>
				<th>MailAddress</th>
				<th>Approved</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (isset($link)) {
					$summat = mysql_query("SELECT * FROM Site");
					while ($row = mysql_fetch_assoc($summat)) {
						$approved = ($row[Approved]) ? "Yes" : "No";
						print "<tr>";
						print "<td id='ID_$row[ID]'>$row[ID]</td>";
						print "<td id='Name_$row[ID]' class='editInPlace_Site'>" . specialchars($row[Name]) . "</td>";
						print "<td id='Address_$row[ID]' class='editInPlace_Site'>" . specialchars($row[Address]) . "</td>";
						print "<td id='Zip_$row[ID]' class='editInPlace_Site'>" . specialchars($row[Zip]) . "</td>";
						print "<td id='City_$row[ID]' class='editInPlace_Site'>" . specialchars($row[City]) . "</td>";
						print "<td id='StreetAddress_$row[ID]' class='editInPlace_Site'>" . specialchars($row[StreetAddress]) . "</td>";
						print "<td id='StreetZip_$row[ID]' class='editInPlace_Site'>" . specialchars($row[StreetZip]) . "</td>";
						print "<td id='StreetCity_$row[ID]' class='editInPlace_Site'>" . specialchars($row[StreetCity]) . "</td>";
						print "<td id='PhoneNo_$row[ID]_Site' class='editInPlace_Site'>" . specialchars($row[PhoneNo]) . "</td>";
						print "<td id='FaxNo_$row[ID]' class='editInPlace_Site'>" . specialchars($row[FaxNo]) . "</td>";
						print "<td id='MailAddress_$row[ID]_Site' class='editInPlace_Site'>" . specialchars($row[MailAddress]) . "</td>";
						print "<td id='Approved_$row[ID]' class='editInPlace_Site_dropdown'>$approved</td>";
						#print "<td id='Su_$row[ID]' class='editInPlace_Site'>$row[SurName]</td>";
						print "<td id='Delete_Site_$row[ID]' style='text-align:right;'><div class='Delete_icon'></div></td></tr>";
					}
				}
			?>
		</tbody>
	</table>
	<div class="ButtonPanel">
		<!--<span style="font-size: 28px"><b>Sites</b></span>--><button class="Button_OpenNew" id="Button_OpenNewSite" onmouseover="tooltip('Key shortcut: Alt+S');" onmouseout="exit();">New <u>S</u>ite</button>
	</div>
</div>

<div id="tabs-3">
	<table id="ComputerTable" class="display">
		<thead>
			<tr>
				<th>ID</th>
				<th>Customer (ID) Name</th>
				<th>Employee (ID) Name</th>
			</tr>
		</thead>
		<tbody>
			<?php
				if (isset($link)) {
					$summat = mysql_query("SELECT * FROM ComputerView");
					while ($row = mysql_fetch_assoc($summat)) {
						print "<tr>";
						print "<td id='ComputerID_$row[ID]'>$row[ID]</td>";
						print "<td id='CustomerID_$row[CustomerID]_ComputerID_$row[ID]' class='editInPlace_Computer_Customer'>" . specialchars($row[Customer]) . "</td>";
						print "<td id='EmployeeID_$row[EmployeeID]_ComputerID_$row[ID]' class='editInPlace_Computer_Employee_dropdown'>" . specialchars($row[Employee]) . "</td>";
						print "</tr>";
					}
				}
			?>
		</tbody>
	</table>


</div>

<div id="tabs-4">
	<div style="height: 400px">
	<div style="width: 55%; float: left; text-align: center;">
		<h2>Promotional codes</h2>
		<table id="PromoCodeTable" class="display">
			<thead>
				<tr>
					<th>ID</th>
					<th>Code</th>
					<th>Starts</th>
					<th>Expires</th>
					<th>Global</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (isset($link)) {
						$summat = mysql_query("SELECT * FROM PromoCode");
						while ($row = mysql_fetch_assoc($summat)) {
							$global = ($row['Global']) ? "Yes" : "No";
							print "<tr>";
							print "<td id='ID_$row[ID]_PromoCode'>$row[ID]</td>";
							print "<td id='PromoCode_$row[ID]' class='editInPlace_PromoCode'>$row[PromoCode]</td>";
							print "<td id='StartDate_$row[ID]' class='editInPlace_PromoCode'>" . date('Y-m-d H:i',strtotime($row['StartDate'])) . "</td>";
							print "<td id='ExpireDate_$row[ID]' class='editInPlace_PromoCode'>" . date('Y-m-d H:i',strtotime($row['ExpireDate'])) . "</td>";
							print "<td id='Global_$row[ID]' class='editInPlace_PromoCode_dropdown'>$global</td>";
							print "<td id='Delete_PromoCode_$row[ID]' style='text-align:right;'><div class='Delete_icon'></div></td></tr>";
						}
					}
				?>
			</tbody>
		</table>
		<div class="ButtonPanel" >
			<!--<span style="font-size: 28px"><b>Promotional codes</b></span>--><button class="Button_OpenNew" id="Button_OpenNewPromoCode" onmouseover="tooltip('Key shortcut: Alt+C');" onmouseout="exit();">New <u>C</u>ode</button>
		</div>
	</div>
	<div style="width: 43%; float: right; text-align: center;">
		<h2>Site codes</h2>
		<table id="PromoCodeSiteTable" class="display">
			<thead>
				<tr>
					<th>Site</th>
					<th>SiteID</th>
					<th>Code</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if (isset($link)) {
						$summat = mysql_query("SELECT * FROM PromoCodeSiteView");
						while ($row = mysql_fetch_assoc($summat)) {
							print "<tr>";
							print "<td id='Site_$row[SiteID]_PromoCodeID_$row[PromoCodeID]' class='editInPlace_PromoCodeSiteView'>" . specialchars($row[Site]) . "</td>";
							print "<td id='SiteID_$row[SiteID]_PromoCodeID_$row[PromoCodeID]' class='editInPlace_PromoCodeSiteView'>$row[SiteID]</td>";
							print "<td id='PromoCode_$row[PromoCodeID]_SiteID_$row[SiteID]' class='editInPlace_PromoCodeSiteView'>$row[PromoCode]</td>";
							print "<td id='Delete_PromoCodeSite_$row[PromoCodeID]:$row[SiteID]' style='text-align:right;'><div class='Delete_icon'></div></td></tr>";
						}
					}
				?>
			</tbody>
		</table>
		<div class="ButtonPanel" >
			<!--<span style="font-size: 28px"><b>Site codes</b></span>--><button class="Button_OpenNew" id="Button_OpenNewPromoCodeSite" onmouseover="tooltip('Key shortcut: Alt+I');" onmouseout="exit();">New S<u>i</u>te code</button>
		</div>
	</div>
	</div>
</div>
</div>

<!--<p>
	<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!"></img></a>
	<a href='http://validator.w3.org/check?uri=referer'><img style="border:0;width:88px;height:31px" src='http://www.w3.org/Icons/valid-html401' alt='Valid HTML 4.01 Transitional'></img></a>
</p>-->

<div id="Dialog_AddEmployee" title="Add new employee">
	<table>
		<tr><td>Site:</td><td><select id="New_Employee_Site"><option></option></select></td></tr>
		<tr><td>Name:</td><td><input type="text" id="New_Employee_Name"/></td></tr>
		<tr><td>PhoneNo:</td><td><input type="text" id="New_Employee_PhoneNo"/></td></tr>
		<tr><td>Comment:</td><td><input type="text" id="New_Employee_Comment"/></td></tr>
		<tr><td>MailAddress:</td><td><input type="text" id="New_Employee_MailAddress"/></td></tr>
		<tr><td>MailAddress2:</td><td><input type="text" id="New_Employee_MailAddress2"/></td></tr>
		<tr><td>Optician:</td><td><select id="New_Employee_Optician"><option value='0'>No</option><option value='1'>Yes</option></select></td></tr>
		<tr><td>Scientist:</td><td><select id="New_Employee_Scientist"><option value='0'>No</option><option value='1'>Yes</option></select></td></tr>
	</table>
</div>

<div id="Dialog_AddSite" title="Add new site">
	<table>
		<tr><td>Name:</td><td><input type="text" id="New_Site_Name"/></td></tr>
		<tr><td>Address:</td><td><input type="text" id="New_Site_Address"/></td></tr>
		<tr><td>Zip:</td><td><input type="text" id="New_Site_Zip"/></td></tr>
		<tr><td>City:</td><td><input type="text" id="New_Site_City"/></td></tr>
		<tr><td>StreetAddress:</td><td><input type="text" id="New_Site_StreetAddress"/></td></tr>
		<tr><td>StreetZip:</td><td><input type="text" id="New_Site_StreetZip"/></td></tr>
		<tr><td>StreetCity:</td><td><input type="text" id="New_Site_StreetCity"/></td></tr>
		<tr><td>PhoneNo:</td><td><input type="text" id="New_Site_PhoneNo"/></td></tr>
		<tr><td>FaxNo:</td><td><input type="text" id="New_Site_FaxNo"/></td></tr>
		<tr><td>OrgRegNo:</td><td><input type="text" id="New_Site_OrgRegNo"/></td></tr>
		<tr><td>Country:</td><td><input type="text" id="New_Site_Country"/></td></tr>
		<tr><td>MailAddress:</td><td><input type="text" id="New_Site_MailAddress"/></td></tr>
	</table>
</div>

<div id="Dialog_AddPromoCode" title="Add new promotional code">
	<table>
		<tr><td>Code:</td><td><input type="text" id="New_PromoCode_Code"/></td></tr>
		<tr><td>Starts:</td><td><input type="text" id="New_PromoCode_Starts"/></td></tr>
		<tr><td>Expires:</td><td><input type="text" id="New_PromoCode_Expires"/></td></tr>
		<tr><td>Global:</td><td><select id="New_PromoCode_Global"><option value='0'>No</option><option value='1'>Yes</option></select></td></tr>
	</table>
</div>


<div id="Dialog_AddPromoCodeSite" title="Connect site with promotional code">
	<table>
		<tr><td>Site:</td><td><select id="New_PromoCodeSite_Site"><option></option></select><button id="toggle" style="display:none;">Show underlying select</button></td></tr>
		<tr><td>Code:</td><td><select id="New_PromoCodeSite_Code"><option></option></select></td></tr>
	</table>
</div>

<div id="Dialog_ReallyDelete" title="Delete">
	Are you sure you want to delete?
</div>

<?php
	if (!isset($link)) {
		echo "<div id='Dialog_Login' title='Login'>Enter password:<input type='password' id='login'/></div>";
		/*<div class="ui-widget">
	        <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
        	    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
            	<strong>Access denied:</strong> You supplied the wrong password.</p>
    	    </div>
	    </div>*/
	}
?>

