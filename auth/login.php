<?php include "includes/header.php" ?>
<?php include "includes/navbar.php" ?>
<div class="site-wrap">
    <!-- ======= Header =======-->

    <main>
        <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light p-2">

            <div class="container">
                <div class="row justify-content-center">
                    <div
                        class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded bg-white p-4 d-flex flex-column flex-md-row">

                        <!-- Left Side: Title -->
                        <div
                            class="w-100 w-md-50 d-flex align-items-center justify-content-center mb-4 mb-md-0 flex-column">
                            <h2 class="text-primary m-0">Login</h2>
                            <h1 class="navbar-brand w-auto" style="font-size:40px">LAREA</h1>
                            <span> <strong>L</strong>everaging <strong>A</strong>necdotal
                                <strong>R</strong>ecords</span>
                            <span>for an <strong>E</strong>ducational <strong>A</strong>ssistant</span>
                        </div>

                        <!-- Right Side: Form -->
                        <div class="w-100 w-md-50 ps-md-4">
                            <form id="loginForm">
                                <div class="mb-3">
                                    <label for="username" class="form-label">UserID</label>
                                    <input type="text" class="form-control" id="userID" placeholder="Enter userID">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password"
                                        placeholder="Enter password">
                                </div>

                                <button type="submit" id="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>

        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission

            const userID = document.getElementById("userID").value.trim(); // Assuming userID is userID
            const password = document.getElementById("password").value.trim();

            if (!userID || !password) {
                Swal.fire({
                    icon: "warning",
                    title: "Missing Fields",
                    text: "Please fill in both fields."
                });
                return;
            }

            $.ajax({
                url: 'functions/login-function.php',
                type: 'POST',
                data: {
                    action: 'login',
                    userID: userID,
                    password: password
                },
                success: function (response) {
                    if (response.status === 'success') {
                        let timerInterval;
                        Swal.fire({
                            title: "Login Complete, Redirecting!",
                            html: "I will close in <b></b> milliseconds And Redirect You to Dashboard.",
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                                const timer = Swal.getPopup().querySelector("b");
                                timerInterval = setInterval(() => {
                                    timer.textContent = `${Swal.getTimerLeft()}`;
                                }, 100);
                            },
                            willClose: () => {
                                clearInterval(timerInterval);
                            }
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.location.href = "../users/index.php";
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Login Failed",
                            text: response.message || "Please try again."
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Server Error",
                        text: "Something went wrong. Please try again."
                    });
                }
            });
        });

    </script>
</div>
<?php include "includes/footer.php" ?>