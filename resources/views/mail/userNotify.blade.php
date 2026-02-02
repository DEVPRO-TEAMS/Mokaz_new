{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur MOKAZ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc3545;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }
        .header img.logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            margin-bottom: 15px;
            color: #343a40;
        }
        .content p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
        }
        .btn-primary {
            background-color: #dc3545;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            margin: 20px 0;
        }
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
        }
        .credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('assets/images/logo/logo-main.png')}}" alt="MOKAZ Logo" class="logo">
            <h1>Bienvenue sur MOKAZ</h1>
        </div>

        <div class="content">
           <center>
                <h2>Bonjour {{ $emailData['nom'] }},</h2>

                <p>Votre compte a bien été créé sur notre plateforme.</p>

                <p>Voici vos identifiants de connexion :</p>

                <div class="credentials">
                    Adresse email : <strong>{{ $emailData['email'] }}</strong><br>
                    Mot de passe : <strong>{{ $emailData['password'] }}</strong>
                </div>

                <p>Pour finaliser votre inscription et accéder à votre espace personnel, cliquez sur le bouton ci-dessous :</p>

                <p class="text-center">
                    <a href="{{ $emailData['url'] }}" class="btn btn-primary">{{ $emailData['buttonText'] }}</a>
                </p>

                <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
                <p><small>{{ $emailData['url'] }}</small></p>

                @if (!empty($emailData['message']))
                    <hr>
                    <p>{{ $emailData['message'] }}</p>
                @endif

                <p>À très bientôt sur la plateforme MOKAZ !</p>
                <p>L’équipe de support.</p>
           </center>
        </div>

        <div class="footer">
            © {{ date('Y') }} MOKAZ by jsbey — Tous droits réservés.<br>
            <a href="#" style="color:#dc3545; text-decoration: none;">Mentions légales</a> |
            <a href="#" style="color:#dc3545; text-decoration: none;">Politique de confidentialité</a>
        </div>
    </div>
</body>
</html> --}}

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bienvenue sur MOKAZ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc3545;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }
        .header img.logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            margin-bottom: 15px;
            color: #343a40;
        }
        .content p {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
        }
        .btn-primary {
            background-color: #dc3545;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            margin: 20px 0;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            margin: 10px 0;
            color: #fff;
        }
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888;
        }
        .credentials {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
        }
        .note {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffeeba;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('assets/images/logo/logo-main.png')}}" alt="MOKAZ Logo" class="logo">
            <h1>Bienvenue sur MOKAZ</h1>
        </div>

        <div class="content">
           <center>
                <h2>Bonjour {{ $emailData['nom'] }},</h2>

                <p>Votre compte a bien été créé sur notre plateforme.</p>

                <p>Voici vos identifiants de connexion :</p>

                <div class="credentials">
                    Adresse email : <strong>{{ $emailData['email'] }}</strong><br>
                    Mot de passe : <strong>{{ $emailData['password'] }}</strong>
                </div>

                <p>Pour finaliser votre inscription et accéder à votre espace personnel, cliquez sur le bouton ci-dessous :</p>

                <p class="text-center">
                    <a href="{{ $emailData['url'] }}" class="btn btn-primary">{{ $emailData['buttonText'] }}</a>
                </p>

                <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
                <p><small>{{ $emailData['url'] }}</small></p>

                @if (!empty($emailData['message']))
                    <hr>
                    <p>{{ $emailData['message'] }}</p>
                @endif

                <!-- NOTE IMPORTANTE -->
                <div class="note">
                    <strong>Important :</strong>  
                    Afin de finaliser votre partenariat avec MOKAZ, veuillez télécharger, lire et conserver votre 
                    <strong>Contrat de Partenariat</strong> en cliquant sur le bouton ci-dessous.
                </div>

                <!-- BOUTON TÉLÉCHARGEMENT CONTRAT -->
                <p class="text-center">
                    <a href="{{ route('contratPrestataire', $emailData['email']) }}" class="btn btn-secondary " download>
                        Télécharger le contrat
                    </a>
                </p>

                <p>À très bientôt sur la plateforme MOKAZ !</p>
                <p>L’équipe de support.</p>
           </center>
        </div>

        <div class="footer">
            © {{ date('Y') }} MOKAZ by jsbey — Tous droits réservés.<br>
            <a href="#" style="color:#dc3545; text-decoration: none;">Mentions légales</a> |
            <a href="#" style="color:#dc3545; text-decoration: none;">Politique de confidentialité</a>
        </div>
    </div>
</body>
</html>

