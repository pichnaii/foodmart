<?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";     
    $dbname = "foodmart";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    $sql_category = "SELECT * FROM categories";
    $result_category = $conn->query($sql_category);

    $sql_slideshow = "SELECT * FROM slideshows WHERE status = 'active' AND imagetype = 1";
    $result_slideshow = $conn->query($sql_slideshow);

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <?php include('include/header.php'); ?>
    <body>
        <?php include('include/head_page.php') ?>
        <section class="py-3" style="background-image: url('images/background-pattern.jpg');background-repeat: no-repeat;background-size: cover;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="banner-blocks">
                            <div class="banner-ad large bg-info block-1">
                                <div class="swiper main-swiper">
                                    <div class="swiper-wrapper">
                                        <?php
                                            if ($result_slideshow->num_rows > 0) {
                                                while($row = $result_slideshow->fetch_assoc()) {
                                        ?>   
                                            <div class="swiper-slide">
                                                <div class="row banner-content p-5">
                                                    <div class="content-wrapper col-md-7">
                                                        <div class="categories my-3"><?php echo strtolower($row['flavor']) ?></div>
                                                        <h3 class="display-4"><?= $row['name'] ?></h3>
                                                        <p><?= $row['description'] ?></p>
                                                        <a href="<?= !empty($row['link_media']) ? 'https://www.' . $row['link_media'] . '.com' : $row['link_page'] . '.php' ?>" target="_blank" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1 px-4 py-3 mt-3">Shop Now</a>
                                                    </div>
                                                    <div class="img-wrapper col-md-5">
                                                        <img src="admin/images/uploads/slideshows/<?= $row['image_path']?>" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php 
                                            } 
                                        } else { ?>
                                            <p>No Data Found.</p>
                                        <?php } ?>
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                        
                            <div class="banner-ad bg-success-subtle block-2" style="background:url('images/ad-image-1.png') no-repeat;background-position: right bottom">
                                <div class="row banner-content p-5">
                                    <div class="content-wrapper col-md-7">
                                        <div class="categories sale mb-3 pb-3">20% off</div>
                                        <h3 class="banner-title">Fruits & Vegetables</h3>
                                        <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24" height="24"><use xlink:href="#arrow-right"></use></svg></a>
                                    </div>
                                </div>
                            </div>

                            <div class="banner-ad bg-danger block-3" style="background:url('images/ad-image-2.png') no-repeat;background-position: right bottom">
                                <div class="row banner-content p-5">
                                    <div class="content-wrapper col-md-7">
                                        <div class="categories sale mb-3 pb-3">15% off</div>
                                        <h3 class="item-title">Baked Products</h3>
                                        <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg width="24" height="24"><use xlink:href="#arrow-right"></use></svg></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- / Banner Blocks -->
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bootstrap-tabs product-tabs">
                            <div class="tabs-header d-flex justify-content-between border-bottom my-5">
                                <h3>Trending Products</h3>
                                <nav>
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <?php
                                        if ($result_category->num_rows > 0) {
                                            while($row = $result_category->fetch_assoc()) {
                                    ?>    
                                        <a href="#" class="nav-link text-uppercase fs-6 active" id="nav-all-tab" data-bs-toggle="tab" data-bs-target="#nav-all"><?php echo $row['name'] ?></a>
                                    <?php 
                                        } 
                                    } else { ?>
                                        <p>No Data Found.</p>
                                    <?php } ?>
                                    </div>
                                </nav>
                            </div>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab">
                                    <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                                        <?php
                                            if ($result->num_rows > 0) {
                                                while($row = $result->fetch_assoc()) {
                                                    $after_discount = floatval($row['price']) - (floatval($row['discount'] ?? 0));
                                        ?>    
                                        <div class="col">
                                            <div class="product-item">
                                                <?= !empty($row['discount']) ? '<span class="badge bg-success position-absolute m-3">-'. $row['discount'] .'$</span>' : '';?>
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <?= !empty($row['image_path']) ? '<img src="admin/images/uploads/'. $row['image_path'] .'"  class="tab-image">' : '<img src="admin/images/uploads/no-image.png">'; ?>
                                                    </a>
                                                </figure>
                                                <h3 class="fw-bold text-uppercase"><?php echo $row['name'];?></h3>
                                                <span class="price">$<?php echo $after_discount ?> / <span class="qty fw-bold"><?php echo $row['unit'];?></span></span>
                                                <?php echo !empty($row['discount']) ? '<span class="price" style="color: #ee1f18;"><del>$'. $row['price'] .'</del></span>' : '<span class="price" style="color: #ee1f18;">No Discount</span>' ?>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <!-- <a href="#" class="btn btn-success">Add to Cart <iconify-icon icon="uil:shopping-cart"></a> -->
                                                    <!-- Add a unique ID and product data to the button -->
                                                    <a href="#" class="btn btn-success addToCart" data-id="<?= $row['id']; ?>" data-name="<?= $row['name']; ?>" data-price="<?= $after_discount; ?>">
                                                        Add to Cart <iconify-icon icon="uil:shopping-cart"></iconify-icon>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                                            } 
                                        } else { ?>
                                            <p>No products found.</p>
                                        <?php } ?>
                                    </div>
                                    <!-- / product-grid -->
                                </div>

                                <div class="tab-pane fade" id="nav-fruits" role="tabpanel" aria-labelledby="nav-fruits-tab">
                                    <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                                        <div class="col">
                                            <div class="product-item">
                                                <span class="badge bg-success position-absolute m-3">-30%</span>
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-cucumber.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <span class="badge bg-success position-absolute m-3">-30%</span>
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-milk.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="col">
                                            <div class="product-item">
                                                <span class="badge bg-success position-absolute m-3">-30%</span>
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-orange-juice.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-raspberries.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-bananas.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-bananas.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- / product-grid -->
                                </div>

                                <div class="tab-pane fade" id="nav-juices" role="tabpanel" aria-labelledby="nav-juices-tab">
                                    <div class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-cucumber.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-milk.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-tomatoes.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-tomatoketchup.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-bananas.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="product-item">
                                                <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                                <figure>
                                                    <a href="index.html" title="Product Title">
                                                        <img src="images/thumb-bananas.png"  class="tab-image">
                                                    </a>
                                                </figure>
                                                <h3>Sunstar Fresh Melon Juice</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                                <span class="price">$18.00</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="input-group product-qty">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                            </button>
                                                        </span>
                                                        <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                                        <span class="input-group-btn">
                                                            <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                                <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- / product-grid -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden d-none">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-header d-flex flex-wrap justify-content-between my-5">
                            <h2 class="section-title">Best selling products</h2>
                            <div class="d-flex align-items-center">
                                <a href="#" class="btn-link text-decoration-none">View All Categories →</a>
                                <div class="swiper-buttons">
                                    <button class="swiper-prev products-carousel-prev btn btn-primary">❮</button>
                                    <button class="swiper-next products-carousel-next btn btn-primary">❯</button>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="products-carousel swiper">
                            <div class="swiper-wrapper">
                                <div class="product-item swiper-slide">
                                    <span class="badge bg-success position-absolute m-3">-15%</span>
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-tomatoes.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <span class="badge bg-success position-absolute m-3">-15%</span>
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-tomatoketchup.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <span class="badge bg-success position-absolute m-3">-15%</span>
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-bananas.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <span class="badge bg-success position-absolute m-3">-15%</span>
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-bananas.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-tomatoes.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-tomatoketchup.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-bananas.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>

                                <div class="product-item swiper-slide">
                                    <a href="#" class="btn-wishlist"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
                                    <figure>
                                        <a href="index.html" title="Product Title">
                                            <img src="images/thumb-bananas.png"  class="tab-image">
                                        </a>
                                    </figure>
                                    <h3>Sunstar Fresh Melon Juice</h3>
                                    <span class="qty">1 Unit</span><span class="rating"><svg width="24" height="24" class="text-primary"><use xlink:href="#star-solid"></use></svg> 4.5</span>
                                    <span class="price">$18.00</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="input-group product-qty">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-left-minus btn btn-danger btn-number" data-type="minus">
                                                <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                            </span>
                                            <input type="text" id="quantity" name="quantity" class="form-control input-number" value="1">
                                            <span class="input-group-btn">
                                                <button type="button" class="quantity-right-plus btn btn-success btn-number" data-type="plus">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </span>
                                        </div>
                                        <a href="#" class="nav-link">Add to Cart <iconify-icon icon="uil:shopping-cart"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / products-carousel -->
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container-fluid">
                <div class="bg-secondary py-5 my-5 rounded-5" style="background: url('images/bg-leaves-img-pattern.png') no-repeat;">
                    <div class="container my-5">
                        <div class="row">
                            <div class="col-md-6 p-5">
                                <div class="section-header">
                                    <h2 class="section-title display-4">Get <span class="text-primary">25% Discount</span> on your first purchase</h2>
                                </div>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Dictumst amet, metus, sit massa posuere maecenas. At tellus ut nunc amet vel egestas.</p>
                            </div>
                            <div class="col-md-6 p-5">
                                <form>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control form-control-lg" name="name" id="name" placeholder="Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-lg" name="email" id="email" placeholder="abc@mail.com">
                                    </div>
                                    <div class="form-check form-check-inline mb-3">
                                        <label class="form-check-label" for="subscribe">
                                            <input class="form-check-input" type="checkbox" id="subscribe" value="subscribe">
                                            Subscribe to the newsletter
                                        </label>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-dark btn-lg">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
            include('include/footer_page.php');
            include('include/footerjs.php');
        ?>

        <script>
            $(document).ready(function() {
                // Handle the "Add to Cart" button click
                $('.addToCart').click(function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    // Get the product data from the button's data attributes
                    var productId = $(this).data('id');
                    var productName = $(this).data('name');
                    var productPrice = $(this).data('price');

                    // Check if the product is already in the cart
                    var existingItem = $(`.checkout-list li[data-id="${productId}"]`);
                    if (existingItem.length > 0) {
                        alert('This product is already in the cart!');
                        return;
                    }

                    // Create a new list item for the product
                    var newItem = `
                        <li class="list-group-item d-flex justify-content-between lh-sm" data-id="${productId}">
                            <div>
                                <h6 class="my-0">${productName}</h6>
                                <small class="text-body-secondary">Brief description</small>
                            </div>
                            <span class="text-body-secondary">$${productPrice}</span>
                        </li>
                    `;

                    // Append the new item to the checkout list
                    $('.checkout-list').append(newItem);

                    // Update the total price
                    updateTotal();
                });

                // Function to calculate and update the total price
                function updateTotal() {
                    var total = 0;
                    $('.checkout-list .text-body-secondary').each(function() {
                        var price = parseFloat($(this).text().replace('$', ''));
                        if (!isNaN(price)) {
                            total += price;
                        }
                    });

                    // Update the total price in the DOM
                    $('.checkout-list strong').text(`$${total.toFixed(2)}`);
                }

                //auto slide
                var swiper = new Swiper('.main-swiper', {
                    loop: true,
                    speed: 800,

                    autoplay: {
                        delay: 2000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    },

                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },

                    grabCursor: true,
                });

                // Optional: pause on hover example with jQuery (Swiper already does it, but just in case)
                $('.main-swiper').hover(
                    function () { swiper.autoplay.stop(); },
                    function () { swiper.autoplay.start(); }
                );
            });
        </script>
    </body>
</html>