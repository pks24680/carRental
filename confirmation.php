<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'go_cars');

$json_data = file_get_contents('cars.json');
$cars = json_decode($json_data, true)['entries'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart']) || !isset($_SESSION['user_details'])) {
        echo "Cart or user details not set.";
        exit;
    }

    $cart = $_SESSION['cart'];
    $user_details = $_SESSION['user_details'];

    $rent_start_date = $cart[0]['start_date'];
    $rent_end_date = $cart[0]['end_date'];
    $status = 'unconfirmed';
    $start = new DateTime($rent_start_date);
    $end = new DateTime($rent_end_date);
    $interval = $start->diff($end);
    $numberOfDays = $interval->days;

    $price = $cart[0]['quantity'] * $cart[0]['price'] * $numberOfDays;

    $user_name = $user_details['name'];
    $user_email = $user_details['email'];

    $query = "INSERT INTO orders (user_name, user_email, rent_start_date, rent_end_date, price, status) 
              VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssssds', $user_name, $user_email, $rent_start_date, $rent_end_date, $price, $status);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;

            $_SESSION['order_id'] = $order_id;
            $_SESSION['model'] = $cart[0]['name'];
            $_SESSION['car_quantity'] = $cart[0]['quantity'];


            unset($_SESSION['cart']);

        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'confirm') {
    if (isset($_SESSION['order_id']) && isset($_SESSION['model']) && isset($_SESSION['car_quantity'])) {
        $order_id = $_SESSION['order_id'];
        $car_model = $_SESSION['model'];
        $car_quantity = $_SESSION['car_quantity'];

        $conn = new mysqli('localhost', 'root', '', 'go_cars');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $query = "UPDATE orders SET status = 'confirmed' WHERE order_id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('i', $order_id);
            if ($stmt->execute()) {
                foreach ($cars as &$car) {
                    if ($car['model'] === $car_model) {
                        $car['quantity'] -= $car_quantity;
                        break;
                    }
                }

                file_put_contents('cars.json', json_encode(['entries' => $cars]));

                unset($_SESSION['order_id']);
                unset($_SESSION['car_model']);
                unset($_SESSION['car_quantity']);
                header('Location: index.php');

            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "No order to confirm or necessary data missing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details - Online Car Rental Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css"
        integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <!-- <script src="https://kit.fontawesome.com/9ad3344363.js" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="assets/css/style.css" />

    </head>

    <body>

    <?php include ('layouts/header_c.php'); ?>
    <div class="container mt-5 pt-5">
        <!-- <h1>ORDER STATUS: UNCONFIRMED</h1> -->
        <h2>CLICK BELOW TO CONFIRM ORDER</h2>
        <a href="confirmation.php?action=confirm">CONFIRM ORDER</a>
        <hr>
        <h3>THANK YOU FOR BROWSING GO CARS</h3>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

    </body>

    </html>