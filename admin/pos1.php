<?php 
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database   = 'foodmart';

    $conn = mysqli_connect($host, $username, $password, $database);

    if (!$conn) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
        exit;
    }

    mysqli_set_charset($conn, 'utf8mb4');

    // ---------- Categories ---------- //
    $categories = [];
    $allowedCategoryIds = [];
    $sqlCat = "SELECT id, name 
               FROM categories 
               WHERE pos_display = 1 
               ORDER BY id";
    $resultCat = mysqli_query($conn, $sqlCat);

    if ($resultCat) {
        while ($row = mysqli_fetch_assoc($resultCat)) {
            $id = (int)$row['id'];
            $categories[] = [
                'id'   => (int)$row['id'],
                'name' => $row['name']
            ];
            $allowedCategoryIds[] = $id;
        }
    } else {
        error_log('Categories query failed: ' . mysqli_error($conn));
    }

    // ---------- Products ---------- //
    $products = [];
    if (!empty($allowedCategoryIds)) {
        // Create a safe comma-separated list of IDs
        $ids = implode(',', array_map('intval', $allowedCategoryIds));

        $sql = "SELECT 
                    p.id, 
                    p.code, 
                    p.name, 
                    p.price, 
                    COALESCE(SUM(pi.quantity), 0) AS quantity,
                    p.unit, 
                    p.status, 
                    p.discount, 
                    p.category_id, 
                    p.image_path AS img
                FROM products p
                LEFT JOIN purchase_items pi ON pi.product_id = p.id
                WHERE p.category_id IN ($ids) 
                  AND p.status = 1
                GROUP BY p.id
                ORDER BY p.id DESC";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['id']          = (int)$row['id'];
                $row['code']        = (int)$row['code'];
                $row['category_id'] = (int)$row['category_id'];
                $row['price']       = (float)$row['price'];
                $row['status']      = (bool)$row['status'];
                $row['discount']    = (float)$row['discount'];

                $filename = basename(str_replace('\\', '/', (string)$row['img']));
                $row['img'] = $filename !== '' ? '../admin/images/uploads/' . $filename : '';
                $row['veg'] = isset($row['veg']) ? (bool)$row['veg'] : true;

                $products[] = $row;
            }
        } else {
            error_log('Product query failed: ' . mysqli_error($conn));
        }
    }
    mysqli_close($conn);



    // $sql = "SELECT id, code, name, price, unit, status, discount, category_id, category_name AS cat , image_path AS img
    //         FROM products ORDER BY id";
    // $result = mysqli_query($conn, $sql);

    // $products = [];
    // if ($result) {
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         // Convert types so JS works correctly
    //         $row['id']          = (int)$row['id'];
    //         $row['code']        = (int)$row['code'];
    //         $row['category_id'] = (int)$row['category_id'];
    //         $row['cat']         = (string)$row['cat'];
    //         $row['price']       = (float)$row['price'];
    //         $row['status']      = (bool)$row['status'];  // 0/1 → true/false  
    //         $row['discount']    =  (float)$row['discount'];


    //         $filename           = basename(str_replace('\\', '/', (string)$row['img']));
    //         $row['img']         = $filename !== '' ? '../admin/images/uploads/' . $filename : '';
    //         $row['veg']         = isset($row['veg']) ? (bool)$row['veg'] : true;

    //         $products[] = $row;
    //     }
    // } else {
    //     error_log('Product query failed: ' . mysqli_error($conn));
    // }
    // // print_r($products);die();
    // mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="icon/braintech.ico" type="image/x-icon">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS --> 
    <link rel="stylesheet" href="css/pos.css">
</head>
<body>
 
    <div class="app-shell">
        <!-- ============ MENU PANEL ============ -->
        <main class="menu-panel">
            <div class="topbar">
                <a class="icon-btn px-3 cursor-pointer"><i class="bi bi-arrow-left-circle-fill"></i></a>
                <button class="icon-btn px-3"><i class="bi bi-house-lock-fill pe-2"></i>Warehouse</button>
                <button class="icon-btn px-3"><i class="bi bi-person-bounding-box pe-2"></i>Customer</button>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search Product here...">
                </div>
            </div>
            <div class="categories" id="categoryList"></div>
            <div class="product-scroll">
                <div class="product-grid" id="productGrid"></div>
            </div>
        </main>
    
        <!-- ============ ORDER PANEL ============ -->
        <aside class="order-panel">
            <div class="order-header">
                <div>
                    <h5>Order Lists</h5>
                </div>
                <button class="edit-btn"><i class="bi bi-bag-check"></i></button>
            </div>
        
            <div class="order-items" id="orderItems"></div>
                <div class="order-summary">
                    <div class="row-line"><span>Sub Total</span><span id="subTotal">$0.00</span></div>
                    <div class="row-line"><span>VAT 10%</span><span id="taxAmount">$0.00</span></div>
                    <div class="row-total"><span>Total Amount</span><span id="totalAmount">$0.00</span></div>
                </div>
                <div class="pay-methods">
                    <button class="active" data-pay="cash"><i class="bi bi-cash-coin"></i>Cash</button>
                    <button data-pay="card"><i class="bi bi-credit-card"></i>Visa Card</button>
                    <button data-pay="qr"><i class="bi bi-qr-code"></i>KHQR</button>
                </div>
            <button class="btn-place" id="placeOrderBtn">Check Out</button>
        </aside>
    </div>

    <!-- ============ ORDER CONFIRMATION MODAL ============ -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Confirm Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group mb-3" id="modalOrderItems"></ul>
                    <div class="d-flex justify-content-between"><span>Sub Total</span><span id="modalSubTotal">$0.00</span></div>
                    <div class="d-flex justify-content-between"><span>Tax</span><span id="modalTax">$0.00</span></div>
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span id="modalTotal">$0.00</span></div>
                    <div class="mt-2 text-muted small">Payment method: <span id="modalPayMethod">Cash</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmOrderBtn">Confirm & Place Order</button>
                </div>
            </div>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 
    <script>
        $(function () {
            /* ---------- DATA ---------- */
            const products = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            const dbCategories = <?= json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

            function buildCategories(products, dbCategories) {
                const counts = {};
                products.forEach(p => {
                    // counts[p.cat] = (counts[p.cat] || 0) + 1;
                    // counts[p.category_id] = (counts[p.category_id] || 0) + 1;
                    if (p.status) {
                        counts[p.category_id] = (counts[p.category_id] || 0) + 1;
                    }
                });
 
                // const iconMap = {
                //     'fruits': 'bi-1-square',
                //     'football boots': 'bi-2-square',
                //     'food & beverage': 'bi-3-square',
                //     'ស្រោមជើង': 'bi-4-square',
                //     'burgers': 'bi-award'
                // };
 
                const list = [
                    // { id: 'all', name: 'All', count: products.length + ' Items', icon: 'bi-grid-3x3-gap-fill' }
                    { 
                        id: 'all', 
                        name: 'All', 
                        count: products.filter(p => p.status).length + ' Items', 
                        icon: 'bi-grid-3x3-gap-fill' 
                    }
                ];
 
                // Object.keys(counts).forEach(catId => {
                //     list.push({
                //         id: catId,
                //         name: catId.charAt(0).toUpperCase() + catId.slice(1),
                //         count: counts[catId] + ' Items',
                //         icon: iconMap[catId.toLowerCase()] || 'bi-basket'
                //     });
                // });

                dbCategories.forEach(cat => {
                    list.push({
                        id: cat.id,                     // use real category id
                        name: cat.name,
                        count: (counts[cat.id] || 0) + ' Items',
                        icon: cat.icon || 'bi-bookmark-heart'
                    });
                });
 
                return list;
            }

            // const categories = buildCategories(products);
            const categories = buildCategories(products, dbCategories);
            let activeCategory = 'all';
            let order = {};       // { productId: qty }
            const TAX_RATE = 0.1;

            const FALLBACK_IMG = 'https://placehold.co/400x300?text=No+Image';
            function imgSrc(p) {
                return p.img && p.img.trim() !== '' ? p.img : FALLBACK_IMG;
            }

            /* ---------- RENDER: CATEGORIES ---------- */
            function renderCategories() {
                const $list = $('#categoryList').empty();
                categories.forEach(c => {
                    const $card = $(`
                        <div class="cat-card ${c.id === activeCategory ? 'active' : ''}" data-cat="${c.id}">
                            <div class="cat-icon"><i class="bi ${c.icon}"></i></div>
                            <div class="cat-name">${c.name}</div>
                        </div>`);
                    $list.append($card);
                });
            }
        
            /* ---------- RENDER: PRODUCTS ---------- */
            function renderProducts() {
                const $grid = $('#productGrid').empty();
                const term = $('#searchInput').val().trim().toLowerCase();
            
                const filtered = products.filter(p => {
                    // const matchesCat = activeCategory === 'all' || p.cat === activeCategory;
                    const matchesCat = activeCategory === 'all' || p.category_id == activeCategory;
                    const matchesSearch = p.name.toLowerCase().includes(term);
                    const matchsCode = p.code.toString().includes(term);
                    return matchesCat && (matchesSearch || matchsCode) && p.status;
                });
            
                if (!filtered.length) {
                    $grid.append('<div class="empty-order" style="grid-column:1/-1;">No products found.</div>');
                    return;
                }
            
                filtered.forEach(p => {
                    const qty = order[p.id] || 0;
                    let discountPrice = p.price * (1 - p.discount / 100);
                    discountPrice = Math.round(discountPrice * 100) / 100;
                    p.discountPrice = discountPrice;
                    const $card = $(`
                        <div class="product-card ${qty > 0 ? 'selected' : ''} ${p.quantity > 0 ? '' : 'd-none'}" data-id="${p.id}">
                            <div class="product-thumb">
                                <img src="${imgSrc(p)}" alt="${p.name}">
                                ${p.discount ? `<span class="badge-discount">${p.discount}%</span>` : ''}
                                <span class="${p.quantity ? 'badge-veg veg' : 'badge-nonveg nonveg'}">
                                    <p class="mb-0">${p.quantity ? p.quantity : 0}</p>
                                </span>
                            </div>
                            <div class="product-title"><span>${p.code}</span> - ${p.name}</div>
                            <div class="product-price-container">
                                <div>
                                    <span class="product-price">$${p.discountPrice.toFixed(2)}</span>
                                    ${p.discount ? `<span class="product-discount">$${p.price.toFixed(2)}</span>` : ''  }
                                </div>
                                <span class="fw-bold">${p.unit}</span>
                            </div>
                            <div class="sizes-label">
                                <label class="pay-option d-none">
                                    <input type="radio" name="payment" value="cash" checked>
                                    <span class="pay-btn">
                                        Cash
                                    </span>
                                </label>
                                <span class="product-size nonstock">39</span>
                                <span class="product-size">40</span>
                                <span class="product-size">41</span>
                                <span class="product-size">42</span>
                                <span class="product-size nonstock">43</span>
                                <span class="product-size">44</span>
                            </div>
                            <div class="btn-area">
                                ${qty > 0 ? `
                                <div class="qty-stepper">
                                    <button class="qty-minus">−</button>
                                    <span>${qty}</span>
                                    <button class="qty-plus">+</button>
                                </div>` : `
                                <button class="btn-add"><i class="bi bi-cart-plus-fill pe-1"></i>Add to Cart</button>`
                                }
                            </div>
                        </div>`);
                    $grid.append($card);
                });
            }
        
            /* ---------- RENDER: ORDER PANEL ---------- */
            function renderOrder() {
                const $items = $('#orderItems').empty();
                const ids = Object.keys(order).filter(id => order[id] > 0);
            
                if (!ids.length) {
                    $items.append('<div class="empty-order"><i class="bi bi-basket3" style="font-size:26px;"></i><br>No items added yet</div>');
                } else {
                    ids.forEach(id => {
                        const p = products.find(x => x.id == id);
                        const price_discount =  p.price * (p.discount / 100);
                        const price =  p.price - price_discount;
                        const qty = order[id];
                        const $item = $(`
                            <div class="order-item" data-id="${id}">
                                <img src="${imgSrc(p)}" alt="${p.name}">
                                <div class="oi-info">
                                    <div class="oi-name"><span>${p.code}</span> - ${p.name}</div>
                                    <div class="oi-price">$${price.toFixed(2)} <span class="oi-qty">${qty}X</span></div>
                                </div>
                                <button class="oi-remove"><i class="bi bi-trash text-danger"></i></button>
                            </div>`);
                        $items.append($item);
                    });
                }
            
                // Totals — rounded to 2 decimals to avoid floating point display errors
                let subtotal = 0;
                ids.forEach(id => {
                    const p = products.find(x => x.id == id);
                    subtotal += p.price * order[id];
                });
                subtotal = Math.round(subtotal * 100) / 100;
                const tax = Math.round(subtotal * TAX_RATE * 100) / 100;
                const total = Math.round((subtotal + tax) * 100) / 100;
            
                $('#subTotal').text('$' + subtotal.toFixed(2));
                $('#taxAmount').text('$' + tax.toFixed(2));
                $('#totalAmount').text('$' + total.toFixed(2));
            }
        
            /* ---------- EVENTS ---------- */
            $('#categoryList').on('click', '.cat-card', function () {
                activeCategory = $(this).data('cat');
                renderCategories();
                renderProducts();
            });
            
            $('#searchInput').on('input', renderProducts);
            
            $('#productGrid').on('click', '.btn-add', function () {
                const id = $(this).closest('.product-card').data('id');
                order[id] = 1;
                renderProducts();
                renderOrder();
            });
            
            $('#productGrid').on('click', '.qty-plus', function () {
                const id = $(this).closest('.product-card').data('id');
                order[id] = (order[id] || 0) + 1;
                renderProducts();
                renderOrder();
            });
            
            $('#productGrid').on('click', '.qty-minus', function () {
                const id = $(this).closest('.product-card').data('id');
                order[id] = Math.max(0, (order[id] || 0) - 1);
                if (order[id] === 0) delete order[id];
                renderProducts();
                renderOrder();
            });
            
            $('#orderItems').on('click', '.oi-remove', function () {
                const id = $(this).closest('.order-item').data('id');
                delete order[id];
                renderProducts();
                renderOrder();
            });
            
            $('.pay-methods').on('click', 'button', function () {
                $('.pay-methods button').removeClass('active');
                $(this).addClass('active');
            });
            
            // $('#placeOrderBtn').on('click', function () {
            //     if (!Object.keys(order).length) {
            //         alert('Please add at least one item to the order.');
            //         return;
            //     }
            //     alert('Order placed for Table 4! Total: ' + $('#totalAmount').text());
            //     order = {};
            //     renderProducts();
            //     renderOrder();
            // });

            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
 
            $('#placeOrderBtn').on('click', function () {
                if (!Object.keys(order).length) {
                    alert('Please add at least one item to the order.');
                    return;
                }
 
                // Populate the modal with a snapshot of the current order
                const $modalItems = $('#modalOrderItems').empty();
                Object.keys(order).filter(id => order[id] > 0).forEach(id => {
                    const p = products.find(x => x.id == id);
                    const qty = order[id];
                    $modalItems.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${p.name} <span class="badge bg-secondary rounded-pill">${qty} × $${p.price.toFixed(2)}</span>
                        </li>`);
                });
 
                $('#modalSubTotal').text($('#subTotal').text());
                $('#modalTax').text($('#taxAmount').text());
                $('#modalTotal').text($('#totalAmount').text());
                $('#modalPayMethod').text($('.pay-methods button.active').text().trim());
 
                orderModal.show();
            });
 
            $('#confirmOrderBtn').on('click', function () {
                orderModal.hide();
                order = {};
                renderProducts();
                renderOrder();
            });

            
            /* ---------- INIT ---------- */
            renderCategories();
            renderProducts();
            renderOrder();
        });
    </script>
</body>
</html>