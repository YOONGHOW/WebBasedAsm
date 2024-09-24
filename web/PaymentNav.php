<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/order_Nav.css">

    <title>Document</title>
</head>

<body>

    <head>
        <nav class="nav_order">
            <a href="?search=all" id="all" class="all">All</a>
            <a href="?search=toPay" id="pay" class="pay">To Pay</a>
            <a href="?search=toShip" id="ship" class="ship">To Ship</a>
            <a href="?search=complete" id="com" class="com">Completed</a>
            <a href="?search=cancelled" id="can" class="can">Cancelled</a>
            <a href="?search=refund" id="refund" class="refund">Return Refund</a>
        </nav>
    </head>
</body>
<script>
    // Get the full URL
    const urlParams = new URLSearchParams(window.location.search);
    var all = document.getElementById('all');
    var pay = document.getElementById('pay');
    var ship = document.getElementById('ship');
    var com = document.getElementById('com');
    var can = document.getElementById('can');
    var refund = document.getElementById('refund');

    // Get the 'message' parameter from the URL
    const message = urlParams.get('search');

    // Display the message in the console (or use it as needed)
    if (message) {
        // Apply styles based on the search value
        if (message == 'all') {
            all.style.backgroundColor = "#e9c28e";
            all.style.borderBottom = "2px solid gray";
        } else if (message == 'toPay') {
            pay.style.backgroundColor = "#e9c28e";
            pay.style.borderBottom = "2px solid gray";
        } else if (message == 'toShip') {
            ship.style.backgroundColor = "#e9c28e";
            ship.style.borderBottom = "2px solid gray";
        } else if (message == 'complete') {
            com.style.backgroundColor = "#e9c28e";
            com.style.borderBottom = "2px solid gray";
        } else if (message == 'cancelled') {
            can.style.backgroundColor = "#e9c28e";
            can.style.borderBottom = "2px solid gray";
        } else if (message == 'refund') {
            refund.style.backgroundColor = "#e9c28e";
            refund.style.borderBottom = "2px solid gray";
        }
    } else {
        console.log('No search parameter found in the URL.');
    }
</script>

</html>