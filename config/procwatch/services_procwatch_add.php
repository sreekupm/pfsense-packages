<?php
/*
	services_procwatch_add.php
	Copyright (C) 2013 Jim Pingle
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
/*
	pfSense_MODULE:	system
*/

##|+PRIV
##|*IDENT=page-services-procwatch-add
##|*NAME=Services: Add ProcWatch Services
##|*DESCR=Allow access to the 'Add ProcWatch Services' page.
##|*MATCH=services_procwatch.php-add*
##|-PRIV

require("guiconfig.inc");
require_once("service-utils.inc");
require_once("procwatch.inc");

if (!is_array($config['installedpackages']['procwatch']['item'])) {
	$config['installedpackages']['procwatch']['item'] = array();
}
$a_pwservices = &$config['installedpackages']['procwatch']['item'];
$a_pwservice_names = array();
foreach ($a_pwservices as $svc) {
	$a_pwservice_names[] = $svc['name'];
}
$system_services = get_services();

unset($input_errors);

if ($_POST) {
	if (!is_numeric($_POST['svcid']))
		

	if (!isset($system_services[$_POST['svcid']])) {
		$input_errors[] = gettext("The supplied service appears to be invalid.");
	}

	if (!$input_errors) {
		$a_pwservices[] = $system_services[$_POST['svcid']];
		procwatch_cron_job();
		write_config();

		header("Location: services_procwatch.php");
		return;
	}
}

$closehead = false;
$pgtitle = array(gettext("Services"),gettext("ProcWatch"), gettext("Add"));
include("head.inc");

?>
<link type="text/css" rel="stylesheet" href="/pfCenter/javascript/chosen/chosen.css" />
<script src="/pfCenter/javascript/chosen/chosen.proto.js" type="text/javascript"></script>
</head>

<body link="#0000CC" vlink="#0000CC" alink="#0000CC">

<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="services_procwatch_add.php" method="post" name="iform" id="iform">
<table width="100%" border="0" cellpadding="6" cellspacing="0" summary="add monitored service">
<tr>
	<td colspan="2" valign="top" class="listtopic"><?=gettext("Add Service Entry"); ?></td>
</tr>
<tr>
	<td width="22%" valign="top" class="vncell"><?=gettext("Service to Add:"); ?></td>
	<td width="78%" class="vtable">
		<select name="svcid" class="formselect" id="svcid">
<?php		$i=0;
		foreach ($system_services as $svc): ?>
			<?php if (!in_array($svc['name'], $a_pwservice_names)): ?>
			<option value="<?= $i ?>"><?=$svc['name'];?>: <?= strlen($svc['description']) > 50 ? substr($svc['description'], 0, 50) . "..." : $svc['description'];?></option>
			<?php endif;
			$i++; ?>
<?php 		endforeach; ?>
		</select>
	</td>
</tr>
<tr>
	<td width="22%" valign="top">&nbsp;</td>
	<td width="78%">
		<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Add"); ?>" /> <input type="button" class="formbtn" value="<?=gettext("Cancel"); ?>" onclick="history.back()" />
	</td>
</tr>
</table>
</form>
<?php include("fend.inc"); ?>
</body>
</html>