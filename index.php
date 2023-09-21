<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Data</title>
    <link rel="stylesheet" href="BankFile.css">
</head>

<body>

    <!-- Video container -->

    <!-- Homepage with a single "Customers" button in the navbar -->
    <div class="container">
        <div class="navbar">
            <h1 class="logo">V BANK <span>-Yes We Can!</span></h1>
            <button class="btn" id="customers-btn">CUSTOMERS</button>
        </div>
    </div>

    <!-- Add a video to the homepage -->

    <!-- Customer Table Container (Initially hidden) -->
    <div class="customer-container" style="display: none;">
        <div class="row">
            <h2 class="table head">CUSTOMER DATA</h2>
            <table class="table">
                <tr>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Details</th>
                </tr>

                <?php
                $conn = mysqli_connect("localhost", "root", "", "Bank");
                if (!$conn) {
                    echo ("Unable to connect to the database server");
                    die();
                }

                // Fetch data from the "customerdata" table (adjust table name if needed)
                $query = "SELECT * FROM customerdata";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo ("Error in executing the query");
                } else {
                    // Display the fetched data in the table
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['Customer Name'] . "</td>";
                        echo "<td>" . $row['Phone'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td><button class='view-btn' data-name='" . $row['Customer Name'] . "' data-email='" . $row['Email'] . "' data-phone='" . $row['Phone'] . "' data-balance='" . $row['Balance'] . "'>View</button></td>";
                        echo "</tr>";
                    }
                }

                // Close the database connection
                mysqli_close($conn);
                ?>

            </table>
        </div>
    </div>

    <!-- Card to display customer details (Initially hidden) -->
    <div id="customer-details" style="display: none;">
        <h3>Customer ID</h3>
        <!-- Customer image will be displayed here -->
        <div class="customerdata">
            <img id="customer-image" src="images/download(1).jpg" alt="Customer Image">
            <div class="customer-info">
                <div>
                    <!-- Customer details -->
                    <strong>Name:</strong> <span id="customer-name"></span><br>
                    <strong>Email:</strong> <span id="customer-email"></span><br>
                    <strong>Phone:</strong> <span id="customer-phone"></span><br>
                    <strong>Balance:</strong> <span id="customer-balance"></span><br>
                    <button id="transfer-btn">Transfer</button> <!-- Add Transfer button -->
                    <button id="transfer-history-btn">Transfer History</button> <!-- Add Transfer History button -->
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Form (Initially hidden) -->
    <div id="transfer-form" style="display: none;">
        <h3>Transaction Form</h3>
        <form id="transfer-form-inner" action="record_transfer.php" method="post">
            <input type="hidden" id="sender-email" name="sender_email" value="">
            <label for="receiver-email">Receiver's Email:</label>
            <input type="email" id="receiver-email" name="receiver_email" required><br>
            <label for="transfer-amount">Amount to Transfer:</label>
            <input type="number" id="transfer-amount" name="amount_transferred" required><br>
            <button type="submit">Confirm Transfer</button>
        </form>
    </div>

    <div id="transfer-history" style="display: none;">
        <!-- Add your transfer history content here -->
        <h3>Transfer History</h3>
        <?php
        if (isset($_GET['customer_email'])) {
            $customerEmail = $_GET['customer_email'];
            $conn = mysqli_connect("localhost", "root", "", "Bank");
            if (!$conn) {
                echo ("Unable to connect to the database server");
                die();
            }

            // Fetch transfer history for the customer where sender or receiver email matches
            $query = "SELECT * FROM transfer_history WHERE sender_email = '$customerEmail' OR customer_email='$customerEmail'";
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "Error: " . mysqli_error($conn);
            } else {
                if (mysqli_num_rows($result) > 0) {
                    echo "<table>";
                    echo "<tr><th>Sender</th><th>Receiver</th><th>Amount Transferred</th></tr>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['sender_email'] . "</td>";
                        echo "<td>" . $row['customer_email'] . "</td>";
                        echo "<td>$" . $row['amount_transferred'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No transfer history available for this customer.</p>";
                }

                mysqli_close($conn);
            }
        } else {
            echo "<p>No customer email provided.</p>";
        }
        ?>
    </div>
    </div>

    <!-- JavaScript to handle the "CUSTOMERS" button click -->
    <script>
        document.getElementById('customers-btn').addEventListener('click', function () {
            // Hide the homepage container
            document.querySelector('.container').style.display = 'none';

            // Show the customer container
            document.querySelector('.customer-container').style.display = 'block';

            // Hide other sections
            document.getElementById('customer-details').style.display = 'none';
            document.getElementById('transfer-form').style.display = 'none';
            document.getElementById('transfer-history').style.display = 'none';
        });
    </script>
    <script>
        const viewButtons = document.querySelectorAll('.view-btn');
        const customerDetails = document.getElementById('customer-details');
        const customerName = document.getElementById('customer-name');
        const customerEmail = document.getElementById('customer-email');
        const customerPhone = document.getElementById('customer-phone');
        const customerBalance = document.getElementById('customer-balance');
        const customerImage = document.getElementById('customer-image');
        const transferForm = document.getElementById('transfer-form');
        const transferHistory = document.getElementById('transfer-history');
        const transferHistoryBtn = document.getElementById('transfer-history-btn');

        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const balance = parseFloat(this.getAttribute('data-balance')).toFixed(2);
                const imageUrl = this.getAttribute('data-image');

                customerName.textContent = name;
                customerEmail.textContent = email;
                customerPhone.textContent = phone;
                customerBalance.textContent = balance;
                customerImage.src = imageUrl;

                customerDetails.style.display = 'block';
                document.querySelector('.customer-container').style.display = 'none';

                transferForm.style.display = 'none';
                transferHistory.style.display = 'none';
            });
        });

        // Handle the "Transfer" button click
        document.getElementById('transfer-btn').addEventListener('click', function () {
            const senderEmail = customerEmail.textContent;
            document.getElementById('sender-email').value = senderEmail;

            transferForm.style.display = 'block';

            customerDetails.style.display = 'none';
            transferHistory.style.display = 'none';
        });

        transferHistoryBtn.addEventListener('click', function () {
            transferHistory.style.display = 'block';

            customerDetails.style.display = 'none';
            transferForm.style.display = 'none';
        });
    </script>
</body>

</html>
