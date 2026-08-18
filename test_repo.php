<?php
require_once 'config/env.php';
require_once 'config/database.php';
require_once 'repositories/BaseRepository.php';
require_once 'repositories/DashboardRepository.php';

try {
    $repo = new DashboardRepository();
    print_r($repo->getPaginatedListings(1, 6));
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
