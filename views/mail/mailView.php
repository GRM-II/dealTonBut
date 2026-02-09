<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #1360AA; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="margin: 0;">DealTonBut</h1>
    </div>

    <div style="background-color: #f4f4f4; padding: 30px; border-radius: 0 0 5px 5px;">
        <h2 style="color: #1360AA;">Réinitialisation de votre mot de passe</h2>

        <p>Bonjour,</p>

        <p>Vous avez demandé la réinitialisation de votre mot de passe sur DealTonBut.</p>

        <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= htmlspecialchars($resetLink) ?>" style="background-color: #1360AA; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Réinitialiser mon mot de passe
            </a>
        </div>

        <p style="color: #666; font-size: 14px;">
            <strong>Ce lien expirera dans 30 minutes.</strong>
        </p>

        <p style="color: #666; font-size: 14px;">
            Si le bouton ne fonctionne pas, copiez ce lien :
            <br>
            <a href="<?= htmlspecialchars($resetLink) ?>" style="color: #1360AA; word-break: break-all;"><?= htmlspecialchars($resetLink) ?></a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="color: #999; font-size: 12px;">
            Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </p>
    </div>
</body>
</html>
