<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
        $cart = json_decode($_POST['cart'], true);

        if (is_array($cart)) {
            $conn = new mysqli("localhost", "root", "", "foodmart");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            foreach ($cart as $item) {
                $name = $item['name'];
                $unit_price = $item['price'];
                $qty = $item['qty'];
                $total_price = $unit_price * $qty;

                $stmt = $conn->prepare("INSERT INTO pos_sale (product_name, unit_price, qty, total_price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdid", $name, $unit_price, $qty, $total_price);
                $stmt->execute();
            }

            $stmt->close();
            $conn->close();

            echo "Success";
        } else {
            echo "Invalid cart data";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<?php include "include/header.php" ?>
<style>
    .pos-card{
        background-color: antiquewhite;
        padding: 5px;
        border-radius: 5px;
    }
    .pos-card img{
        width: 100%;
        height: 9rem;
        object-fit: cover;
        border-radius: 5px;
        background-color: #fff;
    }
    .pos-card h6{
        margin: 0;
        padding: 10px 0 0 0;
        font-size: 1rem;
        font-weight: bold;
    }
    .pos-card p{
        font-size: 1rem;
        font-weight: bold;
        margin: 0;
    }
    .pos-card button{
        width: 100%;
        margin-top: 5px;
    }
</style>
<body>
    <div class="position-relative bg-white d-flex p-0">
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="content" style="margin-left: 0px; width: 100%;">
            <div class="container-fluid" style="padding: 0px;">
                <div class="row m-0 p-0 h-100 g-3">
                    <div class="col-md-8">
                        <div class="bg-light p-0 rounded">
                            <h5 class="text-center fw-bold">Product List</h5>
                            <div class="row g-3">
                                <?php
                                    // Fetch products from the database
                                    $conn = new mysqli("localhost", "root", "", "foodmart");
                                    $result = $conn->query("SELECT * FROM products");
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<div class="col-md-3 col-sm-6">
                                                    <div class="pos-card add-to-cart cursor-pointer" data-id="'. $row['id'] .'" data-name="'. $row['name'] .'" data-price="'. $row['price'] .'">
                                                        <img src="images/uploads/'. $row['image_path'] .'" alt="'. $row['name'] .'" class="img-fluid" />
                                                        <div>
                                                            <h6>'. $row['name'] .'</h6>
                                                            <p>$'. $row['price'] .'</p>
                                                        </div>
                                                    </div>
                                                </div>';
                                        }
                                    } else {
                                        echo "<p class='text-center'>No products available.</p>";
                                    }
                                    $conn->close();
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light p-0 rounded">
                            <div class="table-responsive">
                                <h5 class="text-center fw-bold">Cart</h5>
                                <table class="table table-striped mb-3">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th width="15%">Unit Price</th>
                                            <th width="10%" class="text-center">Qty</th>
                                            <th width="18%">Price</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-items">
                                        <!-- Cart items will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6>Total: $<span id="cart-total">0.00</span></h6>
                                <button class="btn btn-success btn-sm" id="checkout-btn">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "include/foot.php" ?>
    </div>

    <script>
        $(document).ready(function () {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const cartItems = $('#cart-items');
            const cartTotal = $('#cart-total');

            updateCart();

            // Add to Cart functionality
            $('.add-to-cart').on('click', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price'));

                const existingItem = cart.find(item => item.id === id);
                if (existingItem) {
                    existingItem.qty++;
                } else {
                    cart.push({ id, name, price, qty: 1 });
                }
                saveCart();
                updateCart();
            });

            // Update Cart
            function updateCart() {
                cartItems.empty();
                let total = 0;
                cart.forEach(item => {
                    total += item.price * item.qty;
                    cartItems.append(`
                        <tr>
                            <td class="align-middle">${item.name}</td>
                            <td class="text-right align-middle">$ ${item.price}</td>
                            <td class="text-center">
                                <input type="number" class="form-control qty-input" data-id="${item.id}" value="${item.qty}" min="1" style="width: 60px;height: 30px;">
                            </td>
                            <td class="text-right align-middle">$ ${(item.price * item.qty).toFixed(2)}</td>
                            <td class="text-center align-middle"><i class="fa fa-trash text-danger cursor-pointer remove-from-cart" data-id="${item.id}"></i></td>
                        </tr>
                    `);
                });
                cartTotal.text(total.toFixed(2));

                // Attach event listener for quantity input changes
                $('.qty-input').on('change', function () {
                    const id = $(this).data('id');
                    const newQty = parseInt($(this).val());
                    updateItemQty(id, newQty);
                });

                // Attach event listener for remove buttons
                $('.remove-from-cart').on('click', function () {
                    const id = $(this).data('id');
                    removeFromCart(id);
                });
            }

            // Update item quantity
            function updateItemQty(id, newQty) {
                const item = cart.find(item => item.id === id);
                if (item) {
                    item.qty = newQty > 0 ? newQty : 1; // Ensure quantity is at least 1
                    saveCart();
                    updateCart();
                }
            }
            
            // Save Cart to localStorage
            function saveCart() {
                localStorage.setItem('cart', JSON.stringify(cart));
            }

            // Remove from Cart
            function removeFromCart(id) {
                cart = cart.filter(item => item.id !== id);
                saveCart();
                updateCart();
            }

            // Checkout functionality
            $('#checkout-btn').on('click', function () {
                if (cart.length === 0) {
                    alert('Cart is empty!');
                    return;
                }
                let receiptContent = `
                    <h3>Receipt</h3>
                    <table border="1" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                let total = 0;
                cart.forEach(item => {
                    const itemTotal = item.price * item.qty;
                    total += itemTotal;
                    receiptContent += `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.qty}</td>
                            <td>$${item.price.toFixed(2)}</td>
                            <td>$${itemTotal.toFixed(2)}</td>
                        </tr>
                    `;
                });
                receiptContent += `
                        </tbody>
                    </table>
                    <h4>Total: $${total.toFixed(2)}</h4>
                `;

                const receiptWindow = window.open('', '_blank', 'width=400,height=600');
                receiptWindow.document.write(`
                    <html>
                        <head>
                            <title>Receipt</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                                th { background-color: #f2f2f2; }
                            </style>
                        </head>
                        <body>
                            ${receiptContent}
                            <button onclick="window.print()">Print Receipt</button>
                        </body>
                    </html>
                `);
                receiptWindow.document.close();

                $.ajax({
                    url: 'pos.php', // PHP script to handle database insertion
                    method: 'POST',
                    data: { cart: JSON.stringify(cart) },
                    success: function (response) {
                        alert('Checkout successful!');
                        cart = [];
                        saveCart();
                        updateCart();
                    },
                    error: function () {
                        alert('An error occurred during checkout.');
                    }
                });

                // cart = [];
                // saveCart();
                // updateCart();
            });
        });
    </script>
</body>
</html>