<?php
function showError($title = "Error", $message = "Something went wrong.", $image = "assets/images/no-data.png", $redirect = "./", $seconds = 5){
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($title) ?> - HindSafar</title>
    <style>
      body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f4f7fa;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
      }

      .message-box {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 500px;
      }

      .message-box img {
        width: 200px;
        margin-bottom: 20px;
      }

      h2 {
        margin-bottom: 10px;
      }

      p {
        margin-bottom: 20px;
      }

      .btn {
        padding:10px 20px;
        background:#004aad;
        color:white;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
      }
    </style>
    <meta http-equiv="refresh" content="<?= intval($seconds) ?>;url=<?= htmlspecialchars($redirect) ?>">
  </head>
  <body>

    <div class="message-box">
      <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($title) ?>" />
      <h2><?= htmlspecialchars($title) ?></h2>
      <p><?= htmlspecialchars($message) ?></p>
      <a href="<?= htmlspecialchars($redirect) ?>" class="btn">Go to Homepage</a>
      <p style="margin-top:10px; font-size:0.9rem;">Redirecting to homepage in <?= intval($seconds) ?> seconds...</p>
    </div>

  </body>
  </html>
  <?php
  exit();
}
?>
