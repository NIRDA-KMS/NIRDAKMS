

// frontend/index.php - Basic UI
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Chat</title>
    <script src="https://cdn.jsdelivr.net/npm/socket.io-client/dist/socket.io.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/emojionearea@3.4.1/dist/emojionearea.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/emojionearea@3.4.1/dist/emojionearea.min.css">
</head>
<body>
<div id="chat-box"></div>
<textarea id="message"></textarea>
<button onclick="sendMessage()">Send</button>
<script>
    const socket = io("http://localhost:3000");
    document.getElementById("message").emojioneArea();

    socket.on('message', function(msg) {
        document.getElementById('chat-box').innerHTML += `<div>${msg}</div>`;
    });

    function sendMessage() {
        const text = document.querySelector(".emojionearea-editor").innerHTML;
        socket.emit("send_message", text);
    }
</script>
</body>
</html>
