<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #EDF2F7; margin: 0; padding: 0; min-height: 100vh;">
    <table width="100%" cellpadding="0" cellspacing="0" style="min-height: 100vh;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="100%" style="max-width: 600px;" cellpadding="0" cellspacing="0">
                    <!-- Conteneur principal avec effet glassmorphism -->
                    <tr>
                        <td style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(255,255,255,0.2);">
                            
                            <!-- Header avec logo -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 40px 40px 20px; text-align: center;">
                                        <!-- Placeholder pour le logo - remplacez par votre logo -->
                                        <div style="width: 70px; height: 70px; margin: 0 auto 10px; padding: 7px background: #f0fff4; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                                            <img src="{{ url('assets/images/logo/logo-main.png')}}" style="width: 70px; border-radius: 20px;" class="logo" alt="Laravel Logo">
                                            {{-- <div style="color: white; font-size: 32px; font-weight: bold;">
                                            </div> --}}
                                        </div>
                                        {{-- <h1 style="margin: 0; color: #1a202c; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                            {{ config('app.name') }}
                                        </h1> --}}
                                        <p style="margin: 8px 0 0; color: #718096; font-size: 16px; font-weight: 500;">
                                            Réinitialisation de mot de passe
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Contenu principal -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 20px 40px 40px; color: #2d3748;">
                                        <div style="background: #f0fff4; border-radius: 16px; padding: 32px; margin-bottom: 32px; border-left: 4px solid red;">
                                            <h2 style="margin: 0 0 16px; color: #1a202c; font-size: 20px; font-weight: 600;">
                                                Bonjour {{ $user->name ?? 'Utilisateur' }} 👋
                                            </h2>
                                            <p style="margin: 0 0 20px; color: #4a5568; line-height: 1.6; font-size: 16px;">
                                                Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. 
                                                Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe.
                                            </p>
                                        </div>

                                        <!-- Bouton CTA moderne -->
                                        <table width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $url }}" style="display: inline-block; background: linear-gradient(135deg, #ea6666 0%, red 100%); color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; letter-spacing: 0.5px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3); transition: transform 0.2s ease;">
                                                        🔐 Réinitialiser mon mot de passe
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Informations importantes -->
                                        <div style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 12px; padding: 20px; margin: 24px 0;">
                                            <div style="display: flex; align-items: flex-start;">
                                                <span style="color: #e53e3e; font-size: 18px; margin-right: 12px;">⏰</span>
                                                <div>
                                                    <p style="margin: 0; color: #742a2a; font-weight: 600; font-size: 14px;">
                                                        Important : Ce lien expire dans 60 minutes
                                                    </p>
                                                    <p style="margin: 8px 0 0; color: #9b2c2c; font-size: 14px; line-height: 1.5;">
                                                        Pour votre sécurité, ce lien n'est valable que pendant une heure.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Note de sécurité -->
                                        <div style="background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 12px; padding: 20px; margin: 24px 0;">
                                            <div style="display: flex; align-items: flex-start;">
                                                <span style="color: #38a169; font-size: 18px; margin-right: 12px;">🛡️</span>
                                                <div>
                                                    <p style="margin: 0; color: #2f855a; font-weight: 600; font-size: 14px;">
                                                        Vous n'avez pas fait cette demande ?
                                                    </p>
                                                    <p style="margin: 8px 0 0; color: #38a169; font-size: 14px; line-height: 1.5;">
                                                        Aucune action n'est requise. Votre compte reste sécurisé.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lien de secours -->
                                        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
                                            <p style="margin: 0 0 12px; color: #718096; font-size: 14px;">
                                                Le bouton ne fonctionne pas ? Copiez et collez ce lien dans votre navigateur :
                                            </p>
                                            <p style="margin: 0; padding: 12px; background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 12px; color: #4a5568; word-break: break-all;">
                                                {{ $url }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #f7fafc, #edf2f7); padding: 30px 40px; text-align: center; border-top: 1px solid #e2e8f0;">
                                        <p style="margin: 0 0 8px; color: #718096; font-size: 14px; font-weight: 500;">
                                            © {{ date('Y') }} {{ config('app.name') }}
                                        </p>
                                        <p style="margin: 0; color: #a0aec0; font-size: 12px;">
                                            Tous droits réservés. Email automatique, merci de ne pas répondre.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>