<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GO CARS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css"
        integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
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
                            <li>
                                <a class="dropdown-item" href="category.php?category=SUV">SUV</a>
                            </li>
                            <li><a class="dropdown-item" href="category.php?category=Wagon">WAGON</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownShop" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            BROWSE BY BRAND
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownShop">
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
                    <input class="form-control me-2" type="text" name="query" placeholder="Search Cars"
                        aria-label="Search" id="search_suggestions">
                    <div id="suggestions-container" class="position-absolute mt-7"></div>
                    <button class="btn btn-outline-success" type="submit">
                        <span class="visually-hidden">Search</span>
                        <img src="assets/imgs/search-icon.jpg" alt="Search" style="width: 20px; height: 20px;">
                    </button>
                </form>

                <script>
                    $(document).ready(function () {
                        function getRecentKeywords() {
                            return JSON.parse(localStorage.getItem('recentKeywords')) || [];
                        }

                        function updateRecentKeywords(keyword) {
                            var recentKeywords = getRecentKeywords();
                            recentKeywords.unshift(keyword);
                            recentKeywords = recentKeywords.filter((value, index, self) => self.indexOf(value) === index).slice(0, 5);
                            localStorage.setItem('recentKeywords', JSON.stringify(recentKeywords));
                        }

                        function displayRecentKeywords() {
                            var recentKeywords = getRecentKeywords();
                            if (recentKeywords.length > 0) {
                                var html = '<ul>';
                                recentKeywords.forEach(function (keyword) {
                                    html += '<li>' + keyword + '</li>';
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
                    });

                </script>
                <a class="nav-link" href="cart.php">
                    <img src="./assets/imgs/cart.png" alt="Cart" style="width: 30px; height: 30px;">
                </a>
            </div>
        </div>
    </nav>
</body>