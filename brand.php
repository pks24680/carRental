<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Cars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css"
        integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/9ad3344363.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>

    <?php include ('layouts/header.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Cars by Brand</h1>

        <section id="brand-cars">
            <div class="container">
                <div class="row">
                    <?php
                    $brand = isset($_GET['brand']) ? $_GET['brand'] : '';

                    $json_data = file_get_contents('cars.json');
                    $cars = json_decode($json_data, true)['entries'];

                    $filtered_cars = array_filter($cars, function ($car) use ($brand) {
                        return strtolower($car['brand']) === strtolower($brand);
                    });

                    if (!empty($filtered_cars)) {
                        foreach ($filtered_cars as $car) {
                            $button_class = $car['quantity'] > 0 ? 'btn-outline-success' : 'btn-outline-secondary disabled';
                            $button_text = $car['quantity'] > 0 ? 'Rent' : 'Unavailable';
                            $quantity_disabled = $car['quantity'] > 0 ? '' : 'disabled';
                            ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img src="<?php echo $car['image']; ?>" class="card-img-top"
                                        alt="<?php echo $car['model']; ?>">
                                    <div class="card-body">
                                        <form method="POST" action="cart.php?new_selection=true">
                                            <input type="hidden" name="product_id" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_image" value="<?php echo $car['image']; ?>" />
                                            <input type="hidden" name="product_name" value="<?php echo $car['model']; ?>" />
                                            <input type="hidden" name="product_price"
                                                value="<?php echo $car['price_per_day']; ?>" />
                                            <h5 class="card-title"><?php echo $car['model']; ?></h5>
                                            <p class="card-text">Details: <?php echo $car['description']; ?></p>
                                            <p class="card-text">Mileage: <?php echo $car['mileage']; ?></p>
                                            <p class="card-text">Fuel Type: <?php echo $car['fuel_type']; ?></p>
                                            <p class="card-text">Seats: <?php echo $car['seats']; ?></p>
                                            <p class="card-text">Price/Day: $<?php echo $car['price_per_day']; ?></p>
                                            <p class="card-text">Availability: <?php echo $car['quantity']; ?></p>
                                            <button class="btn btn-success" type="submit" name="Rent" <?php if ($car['quantity'] <= 0)
                                                echo 'disabled'; ?>><?php echo $car['quantity'] <= 0 ? 'Unavailable' : 'Rent'; ?></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No cars found for the selected brand.</p>";
                    }
                    ?>
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