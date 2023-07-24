<nav class="navbar navbar-expand-lg" style="background-color: #9E7676">
    <div class="container-fluid">
        <a class="navbar-brand text-warning" href="home.php">Home</a>
        <button class="navbar-toggler navbar-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0" >
               

                <div class="dropdown">
                    <button class="btn dropdown-toggle text-warning" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Customer
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="customer_create.php">Create Customer</a></li>
                        <li><a class="dropdown-item" href="customer_read.php">Customer List</a></li>

                    </ul>
                </div>
               
                <div class="dropdown">
                    <button class="btn dropdown-toggle text-warning" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Product
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="product_create.php">Create Product</a></li>
                        <li><a class="dropdown-item" href="product_read.php">Product List</a></li>

                    </ul>
                </div>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="order_create.php">Order Form</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="order_summary.php">Order Summary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="contact.php">Contact Us</a>
                </li>

            </ul>
            <li class="nav-item navbar-nav">
                <a href='logout.php' class='btn btn-outline-dark'>Log out</a>
            </li>

        </div>
    </div>
</nav>