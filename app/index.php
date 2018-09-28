<?php
require('config.php');
require('jsonToCsv.php');
require('functions.php');
?>
<h1>QWLC Custom Reports</h1>
<div>
  <div>Date Range - </div>
  <div></div>
</div>
<h2>Sales Module</h2>
<div>
  <a href="getOpportunities.php">get Opportunities</a>
</div>
<div>
  <a href="getAppointments.php">get Appointments</a>
</div>
<div>
  <a href="getCollectionsReport.php">get Collection Report</a>
</div>
<h1>Inventory</h1>
<div>
  <a href="getPurchaseOrders.php">get Purchase Orders<a>
</div>
<div>
  <a href="getCollectionsReport.php">get Consumption Report<a>
</div>
<div>
  <a href="getValueReport.php">get Value Report<a>
</div>
<h1>Nextiva</h1>
<div>
  <a href="getOpportunities.php">get Nextiva Inbound Call log</a>
</div>
<div>
  <a href="getOpportunities.php">get Nextiva OutInbound Call log</a>
</div>
<?php 
phpinfo();
