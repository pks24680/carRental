<?php
session_start();

$json_data = file_get_contents('cars.json');
$cars = json_decode($json_data, true)['entries'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['new_selection']) && $_GET['new_selection'] == 'true') {
    $_SESSION['user_details'] = ['name' => '', 'email' => '', 'phone' => '', 'driver_license' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = 1;

    foreach ($cars as $car) {
        if ($car['model'] === $product_id) {
            $product_image = $car['image'];
            $product_name = $car['model'];
            $product_price = $car['price_per_day'];
            $product_availability = $car['quantity'];
            break;
        }
    }

    $_SESSION['cart'] = [
        [
            'id' => $product_id,
            'image' => $product_image,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => $product_quantity,
            'availability' => $product_availability
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_details') {
    $response = ['status' => 'error', 'message' => 'Invalid data'];

    if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['total'])) {
        foreach ($cars as $car) {
            if ($car['model'] === $_POST['product_id']) {
                if ($car['quantity'] < $_POST['quantity']) {
                    $response = [
                        'status' => 'error',
                        'message' => 'The selected quantity is unavailable.',
                        'available_quantity' => $car['quantity']
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                }
            }
        }

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $_POST['product_id']) {
                $item['quantity'] = $_POST['quantity'];
                $item['start_date'] = $_POST['start_date'];
                $item['end_date'] = $_POST['end_date'];
                $item['total'] = $_POST['total'];
                $response = ['status' => 'success', 'message' => 'Details saved'];
                break;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_details'])) {
    $_SESSION['user_details'] = $_POST['user_details'];
    $response = ['status' => 'success', 'message' => 'User details saved'];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'continue') {
    header('Location: index.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    header('Location: index.php');
    exit();
}

$userDetails = isset($_SESSION['user_details']) ? $_SESSION['user_details'] : ['name' => '', 'email' => '', 'phone' => '', 'driver_license' => ''];
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="https://kit.fontawesome.com/9ad3344363.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css" />
    <script>
        $(function () {
            $(".start_date, .end_date").datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function () {
                    saveDetails($(this).closest('tr'));
                    calculateTotal();
                    validateForm();
                }
            });

            $("input[name^='quantities']").on('input', function () {
                saveDetails($(this).closest('tr'));
                calculateTotal();
            });

            $(".start_date, .end_date").on('change', function () {
                saveDetails($(this).closest('tr'));
                calculateTotal();
                validateForm();
            });

            function saveDetails(row) {
                const productId = row.find("input[name^='quantities']").attr('name').split('[')[1].split(']')[0];
                const quantity = row.find("input[name^='quantities']").val();
                const startDate = row.find(".start_date").val();
                const endDate = row.find(".end_date").val();
                const total = calculateTotal();

                $.ajax({
                    url: 'cart.php',
                    method: 'POST',
                    data: {
                        action: 'save_details',
                        product_id: productId,
                        quantity: quantity,
                        start_date: startDate,
                        end_date: endDate,
                        total: total
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'error') {
                            alert(response.message);
                            row.find("input[name^='quantities']").val(response.available_quantity);
                            calculateTotal();
                        }
                        console.log(response.message);
                    }
                });
            }

            function calculateTotal() {
                let total = 0;
                $("tbody tr").each(function () {
                    const startDate = new Date($(this).find(".start_date").val());
                    const endDate = new Date($(this).find(".end_date").val());
                    const days = (endDate - startDate) / (1000 * 60 * 60 * 24);
                    const pricePerDay = parseFloat($(this).find(".price").data("price"));
                    const quantity = parseInt($(this).find("input[name^='quantities']").val());
                    const itemTotal = pricePerDay * quantity * (days > 0 ? days : 0);
                    $(this).find(".item-total").text("$" + itemTotal.toFixed(2));
                    total += itemTotal;
                });
                $("#total-cost").text("$" + total.toFixed(2));
                return total;
            }

            function validateForm() {
                const nameInput = $('#checkout-name').val().trim();
                const emailInput = $('#checkout-email').val().trim();
                const phoneInput = $('#checkout-phone').val().trim();
                const licenseValue = $('#driver-license').val();
                const startDate = $(".start_date").val();
                const endDate = $(".end_date").val();

                const nameError = $('#name-error');
                const emailError = $('#email-error');
                const phoneError = $('#phone-error');
                const licenseError = $('#license-error');

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const phonePattern = /^\d{10}$/;

                let isValid = false;

                if (nameInput.length !== 0 && emailPattern.test(emailInput) && phonePattern.test(phoneInput) && licenseValue === "Yes" && startDate !== '' && endDate !== '') {

                    isValid = true;
                }

                $('#checkout-btn').prop('disabled', !isValid);
            }

            $('#checkout-name, #checkout-email, #checkout-phone, #driver-license').on('input change', validateForm);

            function saveUserDetails() {
                const userDetails = {
                    name: $('#checkout-name').val().trim(),
                    email: $('#checkout-email').val().trim(),
                    phone: $('#checkout-phone').val().trim(),
                    driver_license: $('#driver-license').val()
                };

                $.ajax({
                    url: 'cart.php',
                    method: 'POST',
                    data: {
                        user_details: userDetails
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response.message);
                    }
                });
            }

            $('#checkout-name, #checkout-email, #checkout-phone, #driver-license').on('input change', saveUserDetails);

            validateForm();
        });
    </script>

</head>

<body>

    <?php include ('layouts/header_c.php'); ?>

    <div class="container mt-5 pt-5">
        <h2>Reservation Details</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>No current reservation</p>
        <?php else: ?>
            <form method="post" action="confirmation.php">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Model</th>
                            <th>Price/Day</th>
                            <th>Quantity (Availability)</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $item):
                            $startDate = isset($item['start_date']) ? $item['start_date'] : '';
                            $endDate = isset($item['end_date']) ? $item['end_date'] : '';


                            $start = new DateTime($startDate);
                            $end = new DateTime($endDate);
                            $interval = $start->diff($end);
                            $numberOfDays = $interval->days;


                            $rentalPrice = $item['quantity'] * $item['price'] * $numberOfDays;


                            $total += $rentalPrice;
                            ?>
                            <tr>
                                <td><img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>"
                                        style="width: 80%;"></td>
                                <td><?php echo $item['name']; ?></td>
                                <td class="price" data-price="<?php echo $item['price']; ?>">
                                    $<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo $item['id']; ?>]"
                                        value="<?php echo $item['quantity']; ?>" min="1" class="form-control" />
                                    <p class="card-text">Availability: <?php echo $item['availability']; ?></p>
                                </td>
                                <td>
                                    <input type="text" name="start_dates[<?php echo $item['id']; ?>]"
                                        placeholder="Select start date" class="form-control start_date"
                                        value="<?php echo $startDate; ?>" required>
                                </td>
                                <td>
                                    <input type="text" name="end_dates[<?php echo $item['id']; ?>]"
                                        placeholder="Select end date" class="form-control end_date"
                                        value="<?php echo $endDate; ?>" required>
                                </td>
                                <td class="item-total">$<?php echo number_format($rentalPrice, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
                <h2>User Details</h2>
                <div class="mx-auto container user-details">
                    <div class="form-group checkout-large-element">
                        <label for="checkout-name">Name</label>
                        <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Enter your name"
                            value="<?php echo htmlspecialchars($userDetails['name']); ?>" required />
                        <div class="error-message" id="name-error"></div>
                        <label for="checkout-email">Email</label>
                        <input type="email" class="form-control" id="checkout-email" name="email"
                            placeholder="Enter your email" value="<?php echo htmlspecialchars($userDetails['email']); ?>"
                            required />
                        <div class="error-message" id="email-error"></div>
                    </div>
                    <div class="form-group checkout-large-element">
                        <label for="checkout-phone">Phone</label>
                        <input type="tel" class="form-control" id="checkout-phone" name="phone"
                            placeholder="Enter Mobile Number" value="<?php echo htmlspecialchars($userDetails['phone']); ?>"
                            required />
                        <div class="error-message" id="phone-error"></div>
                        <label for="driver-license">Do you have a valid Driver's License</label>
                        <select class="form-select" id="driver-license" name="driver_license" required>
                            <option value="Select" disabled <?php if ($userDetails['driver_license'] === '')
                                echo 'selected'; ?>>Select</option>
                            <option value="Yes" <?php if ($userDetails['driver_license'] === 'Yes')
                                echo 'selected'; ?>>Yes
                            </option>
                            <option value="No" <?php if ($userDetails['driver_license'] === 'No')
                                echo 'selected'; ?>>No
                            </option>
                        </select>
                        <div class="error-message" id="license-error"></div>
                    </div>
                </div>
        </div>


        <div class="btn-group">
            <a href="cart.php?action=clear" class="btn btn-danger">Clear & Cancel</a>
            <a href="cart.php?action=continue" class="btn btn-success">Continue Browsing</a>
            <button type="submit" class="btn btn-success-stay" id="checkout-btn">Submit</button>
        </div>
        </form>
    <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.getElementById('checkout-name').addEventListener('input', function () {
            const nameInput = this.value.trim();
            const nameError = document.getElementById('name-error');
            if (nameInput.length === 0) {
                nameError.textContent = 'Name is required';
            } else {
                nameError.textContent = '';
            }
        });

        document.getElementById('checkout-email').addEventListener('input', function () {
            const emailInput = this.value.trim();
            const emailError = document.getElementById('email-error');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput)) {
                emailError.textContent = 'Invalid email format';
            } else {
                emailError.textContent = '';
            }
        });

        document.getElementById('checkout-phone').addEventListener('input', function () {
            const phoneInput = this.value.trim();
            const phoneError = document.getElementById('phone-error');
            const phonePattern = /^\d{10}$/;
            if (!phonePattern.test(phoneInput)) {
                phoneError.textContent = 'Phone number must have 10 numerical digits';
            } else {
                phoneError.textContent = '';
            }
        });

        document.getElementById('driver-license').addEventListener('change', function () {
            const licenseValue = this.value;
            const licenseError = document.getElementById('license-error');
            if (licenseValue === "No") {
                licenseError.textContent = "You must have a valid driver's license";
            } else {
                licenseError.textContent = '';
            }
        });
    </script>
</body>

</html>