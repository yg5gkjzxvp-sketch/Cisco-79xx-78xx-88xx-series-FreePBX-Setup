<?php
$csvFile = "contacts.csv";

// Add new contact
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["name"]) && isset($_POST["number"])) {
    $name = trim($_POST["name"]);
    $number = trim($_POST["number"]);
    if ($name !== "" && $number !== "") {
        $fp = fopen($csvFile, "a");
        fputcsv($fp, [$name, $number]);
        fclose($fp);
    }
    header("Location: index.php");
    exit;
}

// Delete contact
if (isset($_GET['delete'])) {
    $deleteIndex = (int)$_GET['delete'];
    $rows = array_map("str_getcsv", file($csvFile));
    if ($deleteIndex > 0 && isset($rows[$deleteIndex])) {
        unset($rows[$deleteIndex]);
        $fp = fopen($csvFile, "w");
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
    header("Location: index.php");
    exit;
}

// Read and sort contacts
$contacts = array_map("str_getcsv", file($csvFile));
if (count($contacts) > 1) {
    $header = array_shift($contacts);
    $sortBy = $_GET['sort'] ?? 'name';

    usort($contacts, function($a, $b) use ($sortBy) {
        $index = $sortBy === 'number' ? 1 : 0;
        return strcasecmp($a[$index], $b[$index]);
    });

    array_unshift($contacts, $header);

    // Write sorted data back to CSV
    $fp = fopen($csvFile, "w");
    foreach ($contacts as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Contact Manager</title>
  <style>
    body { font-family: sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 400px; }
    th, td { border: 1px solid #ccc; padding: 8px; }
    th { background: #eee; }
    th a { text-decoration: none; color: black; }
  </style>
</head>
<body>
  <h1>Thorn Family Phonebook Editor</h1>
  <form method="post">
    <input type="text" name="name" placeholder="Name" required>
    <input type="text" name="number" placeholder="Number" required>
    <button type="submit">Add Contact</button>
  </form>

  <h2>Current Contacts</h2>
  <table>
    <tr>
      <th><a href="?sort=name">Name</a></th>
      <th><a href="?sort=number">Number</a></th>
      <th>Action</th>
    </tr>
    <?php foreach ($contacts as $index => $row): ?>
      <?php if ($index === 0) continue; // skip header ?>
      <tr>
        <td><?= htmlspecialchars($row[0]) ?></td>
        <td><?= htmlspecialchars($row[1]) ?></td>
        <td><a href="?delete=<?= $index ?>" onclick="return confirm('Delete this contact?')">Delete</a></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <p>Phonebook available at: <br>
  <code>http://<?= $_SERVER['SERVER_ADDR'] ?>/cisco-directory.php</code></p>
</body>
</html>
