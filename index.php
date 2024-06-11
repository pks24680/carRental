<?php
$json_data = file_get_contents('cars.json');
$cars = json_decode($json_data, true);
//Loaded json data above

$search_results = [];

if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $search_query = strtolower($search_query);

    foreach ($cars['entries'] as $car) {
        if (
            stripos($car['model'], $search_query) !== false ||
            stripos($car['description'], $search_query) !== false ||
            stripos($car['type'], $search_query) !== false
        ) {
            $search_results[] = $car;
        }
    }
} else {
    $search_results = $cars['entries'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Car Rental Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css"
        integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <nav id="navbar" class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><img src="assets/imgs/logo.jpg" alt="Online Car Rental Store Logo"
                    style="width: 50px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownShop" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            BROWSE BY CATEGORY
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownShop">
                            <li><a class="dropdown-item" href="category.php?category=Sedan">SEDAN</a></li>
                            <li><a class="dropdown-item" href="category.php?category=SUV">SUV</a></li>
                            <li><a class="dropdown-item" href="category.php?category=Wagon">WAGON</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownBrand" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            BROWSE BY BRAND
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownBrand">
                            <li><a class="dropdown-item" href="brand.php?brand=Ford">FORD</a></li>
                            <li><a class="dropdown-item" href="brand.php?brand=Toyota">TOYOTA</a></li>
                            <li><a class="dropdown-item" href="brand.php?brand=Mercedes">MERCEDES</a></li>
                            <li><a class="dropdown-item" href="brand.php?brand=Hyundai">HYUNDAI</a></li>
                            <li><a class="dropdown-item" href="brand.php?brand=Tesla">TESLA</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">ABOUT</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">CONTACT US</a>
                    </li>
                </ul>
                <form class="d-flex" role="search" action="search.php" method="GET" style="padding-right: 10px;">
                    <input class="form-control-2 me-2" type="text" name="query" placeholder="Search Cars"
                        aria-label="Search" id="search_suggestions">

                    <div id="suggestions-container" class="position-absolute mt-7" style="display: none;"></div>
                    <button class="btn btn-outline-success" type="submit">
                        <span class="visually-hidden">Search</span>
                        <img src="assets/imgs/search-icon.jpg" alt="Search" style="width: 20px; height: 20px;">
                    </button>
                </form>
                <a class="nav-link" href="cart.php">
                    <img src="./assets/imgs/cart.png" alt="Cart" style="width: 30px; height: 30px;">
                </a>
            </div>
        </div>
    </nav>

    <div id="background-image">
        <section id="home" class="pt-5">
            <div class="container center-content">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <h1>WELCOME TO GO CARS</h1>
                        <p class="lead">RENT YOUR FAVOURITE CAR WITH US NOW!</p>
                        <a href="#" onclick="scrollToAllProducts()" class="btn btn-shopping-success btn-lg mt-4">SEE
                            COLLECTION</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="container mt-5">
        <h1 id="all-products" class="text-center">All Cars</h1>
        <section id="category">
            <div class="container">
                <div class="row">
                    <?php foreach ($search_results as $car) { ?>
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
                </div>
            </div>
        </section>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            function getRecentKeywords() {
                return JSON.parse(localStorage.getItem('recentKeywords')) || [];
            }

            function updateRecentKeywords(keyword) {
                var recentKeywords = getRecentKeywords();
                recentKeywords.unshift(keyword);
                recentKeywords = recentKeywords.slice(0, 5);
                localStorage.setItem('recentKeywords', JSON.stringify(recentKeywords));
            }

            function displayRecentKeywords() {
                var recentKeywords = getRecentKeywords();
                if (recentKeywords.length > 0) {
                    var html = '<ul class="list-group">';
                    recentKeywords.forEach(function (keyword) {
                        html += '<li class="list-group-item">' + keyword + '</li>';
                    });
                    html += '</ul>';
                    $('#suggestions-container').html(html);
                    $('#suggestions-container').show();
                }
            }
            $('form').submit(function (event) {
                var query = $('#search_suggestions').val().trim();
                if (query !== '') {
                    updateRecentKeywords(query);
                }
            });
            $('#search_suggestions').on('input', function () {
                var query = $(this).val();
                if (query !== '') {
                    $.ajax({
                        url: 'search_suggestions.php',
                        method: 'GET',
                        data: { query: query },
                        success: function (data) {
                            $('#suggestions-container').html(data);
                            $('#suggestions-container').show();
                        }
                    });
                } else {
                    displayRecentKeywords();
                }
            });

            $(document).on('click', '#suggestions-container li', function () {
                var suggestion = $(this).text();
                updateRecentKeywords(suggestion);
                window.location.href = 'search.php?query=' + suggestion;
            });

            $('#search_suggestions').focus(function () {
                var query = $(this).val();
                if (query === '') {
                    displayRecentKeywords();
                }
            });

            $(document).on('click', function (event) {
                if (!$(event.target).closest('#search_suggestions').length) {
                    $('#suggestions-container').hide();
                }
            });

            function scrollToAllProducts() {
                var navbarHeight = document.getElementById('navbar').offsetHeight;
                var allProductsSection = document.getElementById('all-products');
                var offset = 780;
                var targetScrollPosition = allProductsSection.offsetTop - navbarHeight + offset;
                console.log('Navbar height:', navbarHeight);
                console.log('ScrollY:', window.scrollY);
                console.log('All products section offset top:', allProductsSection.offsetTop);
                console.log('Target scroll position:', targetScrollPosition);
                window.scrollTo({
                    top: targetScrollPosition,
                    behavior: 'smooth'
                });
            }
            window.scrollToAllProducts = scrollToAllProducts;
        });

        window.addEventListener('scroll', function () {
            var navbar = document.getElementById('navbar');
            var allProductsSection = document.getElementById('all-products');
            var navbarHeight = navbar.offsetHeight;
            console.log("Navbar height:", navbarHeight);
            console.log("ScrollY:", window.scrollY);
            console.log("All products section offset top:", allProductsSection.offsetTop);

            if (window.scrollY >= allProductsSection.offsetTop - navbarHeight) {
                navbar.style.backgroundColor = 'grey';
            } else {
                navbar.style.backgroundColor = 'black';
            }
        });
    </script>
</body>
</html>
