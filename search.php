<?php
$json_data = file_get_contents('cars.json');
$cars = json_decode($json_data, true)['entries'];

$search_results = [];
$fuzzy_results = [];

if (isset($_GET['query'])) {
    $search_query = strtolower($_GET['query']);

    foreach ($cars as $car) {
        if (
            stripos($car['model'], $search_query) !== false ||
            stripos($car['description'], $search_query) !== false ||
            stripos($car['type'], $search_query) !== false ||
            stripos($car['brand'], $search_query) !== false
        ) {
            $search_results[] = $car;
        }
    }

    if (empty($search_results)) {
        foreach ($cars as $car) {
            $distance = min(
                levenshtein($search_query, strtolower($car['model'])),
                levenshtein($search_query, strtolower($car['type'])),
                levenshtein($search_query, strtolower($car['brand']))
            );

            if ($distance < 4) {
                $fuzzy_results[] = $car;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <?php include ('layouts/header.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">SEARCH RESULTS - OUR FIND</h1>

        <section id="category">
            <div class="container">
                <div class="row">
                    <?php if (!empty($search_results)) { ?>
                        <?php foreach ($search_results as $car) { ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="<?php echo $car['image']; ?>" class="card-img-top"
                                        alt="<?php echo $car['model']; ?>">
                                    <div class="card-body">
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_image" value="<?php echo $car['image']; ?>" />
                                            <input type="hidden" name="product_name" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_price"
                                                value="<?php echo $car['price_per_day']; ?>" />
                                            <h5 class="card-title"><?php echo $car['model']; ?></h5>
                                            <p class="card-text">Details: <?php echo $car['description']; ?></p>
                                            <p class="card-text">Price/day: $<?php echo $car['price_per_day']; ?></p>
                                            <p class="card-text">Mileage: <?php echo $car['mileage']; ?></p>
                                            <p class="card-text">Fuel type: <?php echo $car['fuel_type']; ?></p>
                                            <p class="card-text">Seats: <?php echo $car['seats']; ?></p>
                                            <p class="card-text">Availability: <?php echo $car['quantity']; ?></p>
                                            <button class="btn btn-success" type="submit" name="Rent" <?php if ($car['quantity'] <= 0)
                                                echo 'disabled'; ?>><?php echo $car['quantity'] <= 0 ? 'Unavailable' : 'Rent'; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } elseif (!empty($fuzzy_results)) { ?>
                        <div class="col-12">
                            <h2>NO EXACT MATCHES FOUND. SHOWING SIMILAR RESULTS:</h2>
                        </div>
                        <?php foreach ($fuzzy_results as $car) { ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="<?php echo $car['image']; ?>" class="card-img-top"
                                        alt="<?php echo $car['model']; ?>">
                                    <div class="card-body">
                                        <form method="POST" action="cart.php">
                                            <input type="hidden" name="product_id" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_image" value="<?php echo $car['image']; ?>" />
                                            <input type="hidden" name="product_name" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_price"
                                                value="<?php echo $car['price_per_day']; ?>" />
                                            <h5 class="card-title"><?php echo $car['model']; ?></h5>
                                            <p class="card-text">Details: <?php echo $car['description']; ?></p>
                                            <p class="card-text">Price/day: $<?php echo $car['price_per_day']; ?></p>
                                            <p class="card-text">Mileage: <?php echo $car['mileage']; ?></p>
                                            <p class="card-text">Fuel type: <?php echo $car['fuel_type']; ?></p>
                                            <p class="card-text">Seats: <?php echo $car['seats']; ?></p>
                                            <p class="card-text">Availability: <?php echo $car['quantity']; ?></p>
                                            <button class="btn btn-success" type="submit" name="Rent" <?php if ($car['quantity'] <= 0)
                                                echo 'disabled'; ?>><?php echo $car['quantity'] <= 0 ? 'Unavailable' : 'Rent'; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="col-12">
                            <p>NO RESULTS FOUND.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>