<?php include 'db.php'; ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Submission</title>
</head>
<body>
    <h2>Submit Event</h2>
    <form method="POST" action="submit.php">
        <input type="text" name="title" placeholder="Event Title" required><br>
        <textarea name="description" placeholder="Event Description"></textarea>
        <input type="date" name="event_date" required><br>
        <button type="submit">Submit</button>
    </form>

    <h2>Upcoming Events</h2>
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['title']) . "</strong> on " . $row['event_date'] . "<br>" . htmlspecialchars($row['description']) . "</li>";
        }
        ?>
    </ul>
</body>
</html>