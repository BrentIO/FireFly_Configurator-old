        <div class="sidebar">
            <div class="productName">FireFly Configurator</div>
            <a <?php if($pageName == "breaker"){?>class="active"<?php };?>href="breaker.php">Breakers</a>
            <a <?php if($pageName == "controller"){?>class="active"<?php };?>href="controller.php">Controllers</a>
            <a <?php if($pageName == "switch"){?>class="active"<?php };?>href="switch.php">Switches</a>
            <a <?php if($pageName == "input"){?>class="active"<?php };?>href="input.php">Inputs</a>
            <a <?php if($pageName == "output"){?>class="active"<?php };?>href="output.php">Outputs</a>
            <a <?php if($pageName == "action"){?>class="active"<?php };?>href="action.php">Actions</a>
            <a <?php if($pageName == "firmware"){?>class="active"<?php };?>href="firmware.php">Firmware</a>
            <a <?php if($pageName == "setting"){?>class="active"<?php };?>href="setting.php">Settings</a>
            <div class="child"><a <?php if($pageName == "buttonColor"){?>class="active"<?php };?>href="buttonColor.php">Button Colors</a></div>
            <div class="child"><a <?php if($pageName == "brightnessName"){?>class="active"<?php };?>href="brightnessName.php">Brightness Names</a></div>
            <a <?php if($pageName == "connectivityMap"){?>class="active"<?php };?>href="map.php">Connectivity Map</a>
            <a href="logout.php">Logout</a>
        </div>
      