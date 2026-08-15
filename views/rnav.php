<?php
$item = !empty($action) ? $action : "";
?>
<nav class="navbar-nav">
  <li class="nav-item pt-2">
    <a href="config.php?display=gateway&action=help" class="btn nav-link <?php echo $item === "help" ? "active" : ""; ?>"><i class="fa fa-info-circle "></i>&nbsp;&nbsp;<?php echo _('Help')?></a>
  </li>
  <li class="nav-item pt-2">
    <a href="config.php?display=gateway" class="btn nav-link" <?php echo $item === ""? "active" : ""; ?>><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo _('Home')?></a>
  </li>
  <br>
</nav>
<br>

