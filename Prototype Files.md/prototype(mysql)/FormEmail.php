<?php
// Connect to database
$servername = "localhost";
$username = "root";
$password = "!Rr201612066";
$dbname = "contacts_list";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch up to 10 contacts ordered by end date
$sql = "SELECT product, supplier, end_date, email FROM contacts ORDER BY end_date ASC LIMIT 10";
$contacts = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Automation System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        form { text-align: left; margin-bottom: 20px; }
        label, input, textarea { display: block; width: 100%; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; cursor: pointer; }
        th { background-color: #f2f2f2; cursor: default; }
        .button-group { display: flex; justify-content: space-between; gap: 10px; margin-bottom: 20px; }
        .button-group button { flex: 1; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        .btn-renewal { background-color: #007bff; color: white; }
        .btn-self-renewal { background-color: #28a745; color: white; }
        .btn-chase { background-color: #dc3545; color: white; }
        .btn-main { float: right; background-color: #6c757d; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Email Automation</h2>
        <h3>Send an Email</h3>
        <div class="button-group">
            <button type="button" class="btn-renewal" data-template="renewal_email">Renewal Email</button>
            <button type="button" class="btn-self-renewal" data-template="self_renewal_email">Self-Renewal Email</button>
            <button type="button" class="btn-chase" data-template="renewal_chase">Renewal Chase</button>
        </div>
        <form id="emailForm" method="POST" action="server.php">
            <input type="hidden" name="template" id="templateInput" value="">

            <label for="recipient_email">Recipient Email:</label>
            <input type="email" id="recipient_email" name="recipient_email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="14" required></textarea>

            <label>Contact Information:</label>
            <table id="contactsTable">
                <tr>
                    <th>Product</th><th>Supplier</th><th>End Date</th><th>Email</th>
                </tr>
                <?php if ($contacts && $contacts->num_rows > 0): ?>
                    <?php while ($row = $contacts->fetch_assoc()): ?>
                        <tr data-email="<?php echo htmlspecialchars($row['email']); ?>"
                            data-supplier="<?php echo htmlspecialchars($row['supplier']); ?>"
                            data-end-date="<?php echo htmlspecialchars($row['end_date']); ?>"
                            data-product="<?php echo htmlspecialchars($row['product']); ?>">
                            <td><?php echo htmlspecialchars($row['product']); ?></td>
                            <td><?php echo htmlspecialchars($row['supplier']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No contacts found.</td></tr>
                <?php endif; ?>
            </table>

            <button type="submit" style="margin-top:10px; padding:10px; background:#007bff; color:white; border:none; border-radius:4px; cursor:pointer;">Send Email</button>
            <button type="button" class="btn-main" onclick="window.location.href='Contacts list.html'">Back to Main Page</button>
        </form>
    </div>

    <script>
        const templates = {
            renewal_email: {
                subject: "Renewal Notice for [Item Name]",
                message: `Hi [Name],

Our records indicate this is due to expire at the beginning/end of [MMMM]. I have attached last year’s figures for reference.

Please let us know if you plan to renew it or not.

Due to the University’s current financial situation, it is important to consider the following factors before renewing:
• What is the roadmap? Will its usage increase or decrease in the coming years?
• Would a multi-year renewal be more cost-effective?
• Do we have other applications or systems that provide the same functionality?
• Are there any more cost-effective suppliers available?
• Only renew what is required to avoid unnecessary expenses.

If you decide to renew, please let us know if we should use cost code CCCC####.
We will then request a quote, which we will send you for your approval before proceeding.`
            },
            self_renewal_email: {
                subject: "Self-Renewal Notice for [Item Name]",
                message: `Hi [Name],

Our records indicate this is due to expire at the beginning/end of [MMMM].

Please let us know if you plan to renew it or not.

If you decide to renew, please inform us of the PO number once raised so we can update our records accordingly.

Due to the University’s current financial situation, it is important to consider the following factors before renewing:
• What is the roadmap? Will its usage increase or decrease in the coming years?
• Would a multi-year renewal be more cost-effective?
• Do we have other applications or systems that provide the same functionality?
• Are there any more cost-effective suppliers available?
• Only renew what is required to avoid unnecessary expenses.

If you need any more help, please don’t hesitate to let us know.`
            },
            renewal_chase: {
                subject: "Renewal Chase for [Item Name]",
                message: `Hi [Name],

Since the expiry date is only [WEEKS_LEFT] weeks away, please let us know if there is any update.

If you want to renew, please let us know that you are happy with the quote and which cost code to use.

If you aren’t planning to renew, please let us know so we can inform the supplier and update our records accordingly.`
            }
        };

        document.querySelectorAll('.button-group button').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.getAttribute('data-template');
                const template = templates[key];
                document.getElementById('templateInput').value = key;
                document.getElementById('subject').value = template.subject;
                document.getElementById('message').value = template.message;
            });
        });

        document.querySelectorAll('#contactsTable tr[data-email]').forEach(row => {
            row.addEventListener('click', () => {
                const email = row.getAttribute('data-email');
                const supplier = row.getAttribute('data-supplier');
                const endDate = row.getAttribute('data-end-date');
                const product = row.getAttribute('data-product');
                const key = document.getElementById('templateInput').value;
                const template = templates[key];

                // Get base fresh copies
                let baseSub = template.subject;
                let baseMsg = template.message;

                const today = new Date();
                const end = new Date(endDate);
                const weeksLeft = Math.ceil((end - today) / (1000 * 60 * 60 * 24 * 7));
                const monthName = end.toLocaleString('en-US', { month: 'long' });

                document.getElementById('recipient_email').value = email;
                const sub = baseSub.replace(/\[Item Name\]/g, product)
                                   .replace(/\[Name\]/g, supplier)
                                   .replace(/\[MMMM\]/g, monthName)
                                   .replace(/\[WEEKS_LEFT\]/g, weeksLeft);
                document.getElementById('subject').value = sub;

                const msg = baseMsg.replace(/\[Item Name\]/g, product)
                                   .replace(/\[Name\]/g, supplier)
                                   .replace(/\[MMMM\]/g, monthName)
                                   .replace(/\[WEEKS_LEFT\]/g, weeksLeft);
                document.getElementById('message').value = msg;
            });
        });
    </script>
</body>
</html>