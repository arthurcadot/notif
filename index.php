<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Notification Push</title>
</head>
<body>
  <h1>Envoyer une notification</h1>
  <input type="text" id="message" placeholder="Votre message ici" autofocus>
  <select id="device">
    <?php
    $token = 'o.05jBdESaPhkT3JKaUfHDyvzq3XSK3zjq';
    $ch = curl_init('https://api.pushbullet.com/v2/devices');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Access-Token: ' . $token]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (!isset($data['devices'])) {
        echo '<option disabled>Erreur : impossible de récupérer les appareils.</option>';
    } else {
        foreach ($data['devices'] as $device) {
            if (!$device['active']) continue;
            $name = htmlspecialchars($device['nickname'] ?? 'Sans nom');
            $iden = htmlspecialchars($device['iden']);
            echo "<option value=\"$iden\">$name</option>";
        }
    }
    ?>
  </select>

  <button onclick="envoyerNotification()">Envoyer</button>
  <p id="status"></p>

  <script>
    function envoyerNotification() {
      const message = document.getElementById('message').value.trim();
      const device = document.getElementById('device').value;
      if (!message) {
        document.getElementById('status').textContent = 'Veuillez saisir un message.';
        return;
      }

      const payload = {
        titre: 'Notification personnalisée',
        text: message,
        iden: device
      };

      fetch('api.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('status').textContent = '✅ Notification envoyée !';
          setTimeout(() => {
            document.getElementById('status').textContent = '';
          }, 3000);
          document.getElementById('message').value = '';
        } else {
          document.getElementById('status').textContent = '❌ Erreur : ' + data.error;
        }
      })
      .catch(error => {
        document.getElementById('status').textContent = '⚠️ Erreur réseau : ' + error;
      });
    }
  </script>
</body>
</html>
