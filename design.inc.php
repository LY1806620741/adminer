<?php
function page_header($title, $breadcrumb = array(), $title2 = "") {
	global $SELF, $LANG;
	header("Content-Type: text/html; charset=utf-8");
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $LANG; ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="robots" content="noindex" />
<title><?php echo $title . (strlen($title2) ? ": " . htmlspecialchars($title2) : "") . " - " . lang('phpMinAdmin') . " 1.3.2-dev"; ?></title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="default.css" />
<?php if ($_COOKIE["highlight"] == "jush") { ?>
<style type="text/css">@import url(http://jush.info/jush.css);</style>
<script type="text/javascript" src="http://jush.info/jush.js" defer="defer"></script>
<script type="text/javascript">window.onload = function () { if (typeof jush != 'undefined') jush.highlight_tag('pre'); }</script>
<?php } ?>
</head>

<body>

<div id="content">
<?php
	if (isset($breadcrumb)) {
		$link = substr(preg_replace('~db=[^&]*&~', '', $SELF), 0, -1);
		echo '<p id="breadcrumb"><a href="' . (strlen($link) ? htmlspecialchars($link) : ".") . '">' . (isset($_GET["server"]) ? htmlspecialchars($_GET["server"]) : lang('Server')) . '</a> &gt; ';
		if (is_array($breadcrumb)) {
			if (strlen($_GET["db"])) {
				echo '<a href="' . substr($SELF, 0, -1) . '">' . htmlspecialchars($_GET["db"]) . '</a> &gt; ';
			}
			foreach ($breadcrumb as $key => $val) {
				if (strlen($val)) {
					echo '<a href="' . htmlspecialchars($SELF) . "$key=" . urlencode($val) . '">' . htmlspecialchars($val) . '</a> &gt; ';
				}
			}
		}
		echo "$title</p>\n";
	}
	echo "<h2>$title" . (strlen($title2) ? ": " . htmlspecialchars($title2) : "") . "</h2>\n";
	if ($_SESSION["message"]) {
		echo "<p class='message'>$_SESSION[message]</p>\n";
		$_SESSION["message"] = "";
	}
	if (isset($_SESSION["databases"][$_GET["server"]]) && !isset($_GET["sql"])) {
		session_write_close();
	}
}

function page_footer($missing = false) {
	global $SELF, $mysql;
	?>
</div>

<div id="menu">
<h1><a href="http://phpminadmin.sourceforge.net"><?php echo lang('phpMinAdmin'); ?></a></h1>
<?php switch_lang(); ?>
<?php if ($missing != "auth") { ?>
<p>
<a href="<?php echo htmlspecialchars($SELF); ?>sql="><?php echo lang('SQL command'); ?></a>
<a href="<?php echo htmlspecialchars($SELF); ?>dump=<?php echo urlencode($_GET["table"]); ?>"><?php echo lang('Dump'); ?></a>
<a href="<?php echo htmlspecialchars(preg_replace('~db=[^&]*&~', '', $SELF)); ?>logout="><?php echo lang('Logout'); ?></a>
</p>
<form action="">
<p><?php if (strlen($_GET["server"])) { ?><input type="hidden" name="server" value="<?php echo htmlspecialchars($_GET["server"]); ?>" /><?php } ?>
<select name="db" onchange="this.form.submit();"><option value="">(<?php echo lang('database'); ?>)</option>
<?php
		if (!isset($_SESSION["databases"][$_GET["server"]])) {
			flush();
			$_SESSION["databases"][$_GET["server"]] = get_vals("SHOW DATABASES");
		}
		echo optionlist($_SESSION["databases"][$_GET["server"]], $_GET["db"]);
		?>
</select>
<?php if (isset($_GET["sql"])) { ?><input type="hidden" name="sql" value="" /><?php } ?>
<?php if (isset($_GET["schema"])) { ?><input type="hidden" name="schema" value="" /><?php } ?>
</p>
<noscript><p><input type="submit" value="<?php echo lang('Use'); ?>" /></p></noscript>
</form>
<?php
		if ($missing != "db" && strlen($_GET["db"])) {
			$result = $mysql->query("SHOW TABLE STATUS");
			if (!$result->num_rows) {
				echo "<p class='message'>" . lang('No tables.') . "</p>\n";
			} else {
				echo "<p>\n";
				while ($row = $result->fetch_assoc()) {
					echo '<a href="' . htmlspecialchars($SELF) . 'select=' . urlencode($row["Name"]) . '" title="' . lang('%d row(s)', $row["Rows"]) . '">' . lang('select') . '</a> ';
					echo '<a href="' . htmlspecialchars($SELF) . (isset($row["Engine"]) ? 'table' : 'view') . '=' . urlencode($row["Name"]) . '" title="' . (isset($row["Engine"]) ? htmlspecialchars($row["Engine"]) : lang('View')) . '">' . htmlspecialchars($row["Name"]) . "</a><br />\n";
				}
				echo "</p>\n";
			}
			echo '<p><a href="' . htmlspecialchars($SELF) . 'create=">' . lang('Create new table') . "</a></p>\n";
			$result->free();
		}
	}
	?>
</div>

</body>
</html>
<?php
}
