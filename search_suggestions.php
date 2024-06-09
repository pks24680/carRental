<?php
$json_data = file_get_contents('cars.json');
$cars = json_decode($json_data, true)['entries'];

if (isset($_GET['query'])) {
    $search_query = strtolower($_GET['query']);
    $suggestions = [];

    foreach ($cars as $car) {
        if (
            stripos($car['model'], $search_query) !== false ||
            stripos($car['description'], $search_query) !== false ||
            stripos($car['type'], $search_query) !== false
        ) {
            $suggestions[] = $car['model'];
        }
    }

    if (!empty($suggestions)) {
        echo '<ul>';
        foreach ($suggestions as $suggestion) {
            echo '<li>' . $suggestion . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No suggestions found.</p>';
    }
}
