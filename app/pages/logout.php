<?php
session_start();
session_destroy();
header("Location: /kpi-app/public/login");
exit;
