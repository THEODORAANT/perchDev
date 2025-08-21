<?php
// Front-end package builder form
include('../perch/runtime.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Package Builder</title>
</head>
<body>
<form id="package-form">
    <label for="billing">Billing type:</label>
    <select id="billing" name="billing">
        <option value="prepaid">Prepaid</option>
        <option value="monthly">Monthly</option>
    </select>
    <button type="submit">Create Package</button>
</form>
<script>
const form = document.getElementById('package-form');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const billing = document.getElementById('billing').value;
    try {
        const res = await fetch('/api/create-package.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({billing})
        });
        const data = await res.json();
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            alert('Package created with ' + billing + ' billing');
        }
    } catch (err) {
        alert('Request failed');
    }
});
</script>
</body>
</html>
