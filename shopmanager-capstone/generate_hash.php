<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> " . htmlspecialchars($password) . "</p>";
echo "<p><strong>Bcrypt Hash:</strong></p>";
echo "<textarea style='width:100%; height:80px; font-family:monospace;'>" . $hash . "</textarea>";
echo "<p>Copy the hash above and use it in your SQL.</p>";
?>
