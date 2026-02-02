<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Contrat de Partenariat</title>
    <style>
        * {
            font-weight: bold;
            /* font-size: 12.00px;  */
            font-family: Calibri, sans-serif;
            text-align: justify;
        }

        body {
            /* font-family: "Times New Roman", Times, serif; */
            /* font-size: 12px; */
            margin: 40px;
            line-height: 1.6;
            /* color: #000; */

        }

        h1 {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        p {
            margin: 0 0 10px 0;
            /* left: 70.82px;
            top: 683.57px;
            font-size: 12.00px;
            font-family: Calibri, sans-serif;
            transform: scaleX(1.22305); */

        }

        .footer {
            margin-top: 40px;
            /* font-size: 15px; */
            text-align: left;
        }

        .signature {
            margin-top: 60px;
        }

        .signature div {
            display: inline-block;
            width: 48%;
            text-align: center;
        }

        .header {
            /* text-align: center; */
            /* margin-bottom: 15px; */
            /* border-bottom: 2px solid #dc3545; */
            /* padding-bottom: 5px; */
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <div class="header" style="width: 25%; margin: 0">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo/logo-jsbey.png'))) }}"
            style="width: 100%" alt="MOKAZ Logo" class="logo">
    </div>

    {{-- <h1><u>Contrat de Partenariat</u></h1> <br><br>

    <p><strong>Entre les soussignés :</strong></p>

    <p>1) <strong>JSBEY</strong> entreprise société à responsabilité limitée inscrite au RCCM CI-ABJ-03-2024-B13-01106
        établie et ayant son siège social à Anyama Cité Alliance Akwaba – Lot 317 – IM 17 – n°13 – 11 BP 1713 Abidjan 11
        – Tél : 0787245197 / 0767964144 – représentée par Mr YAPI BEN</p>

    <p>Ci-après désignée « société gérante de l'application MOKAZ » d'une part</p><br>

    <p>2) <br> a) Nom de la structure : <span
            style="text-transform: uppercase">{{ $partner->raison_social ?? '' }}</span> </p>
    <p>b) Nom du Propriétaire ou gérant : <span style="text-transform: uppercase">{{ $user->lastname ?? '' }}
            {{ $user->name ?? '' }}</span> </p>
    <p>c) Téléphone : <span>{{ $user->phone ?? '' }}</span> </p>
    <p>d) Adresse mail : <span>{{ $user->email ?? '' }}</span> </p>

    <p>Ci-après désignée « Structure d’hébergement » d'autre part,</p>

    <p>Il a été convenu et arrêté ce qui suit :</p><br>

    <p><strong>Article 1er – Objet du contrat</strong><br><br>
        1) La présente convention a pour objet de définir les conditions dans lesquelles la société JSBEY, société
        gérante de la plateforme MOKAZ, demande à une structure d’hébergement de mettre à la disposition du Client un
        hébergement situé à <span style="text-transform: uppercase">{{ $partner->adresse ?? '' }}</span></p> --}}
    <h1><u>Contrat de Partenariat</u></h1> <br><br>

    <p><strong>Entre les soussignés :</strong></p>

    <p>1) <strong>JSBEY</strong> entreprise société à responsabilité limitée inscrite au RCCM CI-ABJ-03-2024-B13-01106
        établie et ayant son siège social à Anyama Cité Alliance Akwaba – Lot 317 – IM 17 – n°13 – 11 BP 1713 Abidjan 11
        – Tél : 0787245197 / 0767964144 – représentée par Mr YAPI BEN</p>

    <p>Ci-après désignée « société gérante de l'application MOKAZ » d'une part</p><br>

    <p>2) <br> a) Nom de la structure : <span
            style="text-transform: uppercase">{{ $partner->raison_social ?? '' }}</span> </p>
    <p>b) Nom du Propriétaire ou gérant : <span style="text-transform: uppercase">{{ $user->lastname ?? '' }}
            {{ $user->name ?? '' }}</span> </p>
    <p>c) Téléphone : <span>{{ $user->phone ?? '' }}</span> </p>
    <p>d) Adresse mail : <span>{{ $user->email ?? '' }}</span> </p>

    <p>Ci-après désignée « Structure d'hébergement » d'autre part,</p>

    <p><strong>Il a été convenu et arrêté ce qui suit :</strong></p><br>

    <p><strong>Article 1er – Objet du contrat</strong><br><br>
        1) La présente convention a pour objet de définir les conditions dans lesquelles la société JSBEY, société
        gérante de la plateforme MOKAZ, demande à une structure d'hébergement de mettre à la disposition du Client un
        hébergement situé à <span style="text-transform: uppercase">{{ $partner->adresse ?? '' }}</span>
    </p>
    <br><br><br><br><br><br><br><br>

    <p><strong>Article 2 – Obligations de la structure d'hébergement</strong><br><br>
    La structure d'hébergement s'engage à observer la confidentialité la plus totale en ce qui concerne le contenu de la mission et toutes les informations ainsi que tous les documents que la société gérante de l'application et le Client lui auront communiqués dans le cadre légal fixé par « la loi n°2013-450 du 19 Juin 2013 relative à la protection des données à caractère personnel. »<br>
    La structure d'hébergement s'engage à tout mettre en œuvre pour réaliser correctement l'objet du contrat.<br>
    La structure d'hébergement à l'obligation de préserver l'image et les coordonnées du client.<br>
    La structure d'hébergement s'engage à gérer les entrées et les sorties des clients.<br>
    La structure d'hébergement constate l'état des lieux avec le client.</p><br>

    <p><strong>Article 3 – Obligations de la société gérante de l'application MOKAZ</strong><br><br>
        La Société gérante des services décrits à l'article « Objet du contrat » ci-dessus s'engage expressément à fournir, pendant toute la durée du présent contrat, à la structure d'hébergement, toutes les informations, tous les renseignements, tous les documents et toute l'assistance raisonnablement nécessaire pour lui permettre de réaliser l'objet du contrat et d'assurer, dans de bonnes conditions, la fourniture desdits services.<br><br>
        Elle s'engage à promouvoir et à faire la publicité des appartements existants sur sa plateforme. A avoir des échanges avec les clients. À apporter de l'expertise aux structures d'hébergement.</p><br>

    <p><strong>Article 4 – Rémunération des prestations</strong><br><br>
        La commission de la prestation fournie par la société gérante sera de 10% du prix de l'hébergement proposé par la structure d'hébergement. Le taux de la commission est lié à l'indice du coût de la vie donc peut subir soit une diminution soit une valorisation mais toujours en négociation avec la structure d'hébergement pour la partie la concernant.</p><br><br><br>

    <p><strong>Article 5 – Les modalités de règlement</strong><br><br>
        La commission de 10% pour la prestation de la société gérante est prélevée lors de la réservation de l'appartement par un paiement digital. Le reste du paiement du prix du séjour dans l'hébergement se fait soit par paiement digital ou par espèces sur place au gérant de l'hébergement.</p><br>

    <p><strong>Article 6 – Les modalités de remboursement (Recouvrement)</strong><br><br>
        • Réservation inférieure à 7 jours avant l'entrée dans le logement (Pas d'annulation possible après le paiement); pas de remboursement.<br><br>
        • Réservation supérieure ou égale à 7 jours avant l'entrée dans le logement (Annulation possible sous 24h après la réservation); remboursement à 80% après annulation via le mode de paiement.</p><br>

    {{-- <p><strong>Article 2 – Obligations de la structure d’hébergement</strong><br><br>
        La structure d’hébergement s’engage à observer la confidentialité la plus totale en ce qui concerne le contenu
        de la mission et toutes les informations ainsi que tous les documents que la société gérante de l'application et
        le Client lui auront communiqués.<br>
        La structure d’hébergement s’engage à tout mettre en œuvre pour réaliser correctement l’objet du contrat.<br>
        La structure d’hébergement a l’obligation de préserver l’image et les coordonnées du Client.<br>
        La structure d’hébergement s’engage à gérer les entrées et les sorties des locaux.<br>
        La structure d’hébergement constate l’état des lieux avec le Client.</p><br>

        <p><strong>Article 3 – Obligations de la société gérante de l'application MOKAZ</strong><br><br>
            La Société gérante des services décrits à l’article « Objet du contrat » ci-dessus s’engage expressément à
            fournir, pendant toute la durée du présent contrat, à la structure d’hébergement, toutes les informations, tous
            les renseignements, tous les documents et toute l’assistance raisonnablement nécessaire pour lui permettre de
            réaliser l’objet du contrat et d’assurer, dans de bonnes conditions, la fourniture desdits services.<br>
            Elle s’engage à promouvoir et à faire la publicité des appartements existants sur sa plateforme, à avoir des
            échanges avec les clients, à apporter de l’expertise aux structures.</p><br>

        <p><strong>Article 4 – Rémunération des prestations</strong><br><br>
            La commission de la prestation fournie par la société gérante sera de 10% du prix de l’hébergement proposé par
            la structure d’hébergement. Le taux de la commission est lié à l’indice du coût de la vie, donc peut subir soit
            une diminution soit une valorisation, mais toujours en négociation avec la structure d’hébergement pour la
            partie la concernant.</p><br><br><br><br><br>

        <p><strong>Article 5 – Les modalités de règlement</strong><br><br>
            La commission de 10% pour la prestation de la société gérante est prélevée lors de la réservation de
            l’appartement par un paiement digital. Le reste du paiement du prix du séjour dans l’hébergement se fait soit
            par paiement digital ou par espèces sur place au gérant de la structure.</p><br>

        <p><strong>Article 6 – Les modalités de remboursement (Recouvrement)</strong><br><br>
        • Réservation à partir de 01 à 03 jours avant l’entrée dans le logement : Pas d’annulation possible après le
        paiement ; pas de remboursement.<br>
        • Réservation à partir de 04 à 06 jours avant l’entrée dans le logement : Annulation possible sous 24h après la
        réservation ; remboursement après annulation via le mode de paiement.<br>
        • Réservation à partir de 07 jours et au-delà : Annulation possible jusqu’à 48h après la réservation ;
        remboursement après annulation via le mode de paiement.
    </p><br> --}}

    
    <p><strong>Article 7 – Durée du contrat</strong><br><br>
        Le présent contrat est conclu pour une durée de six mois, reconductible tacitement. Chaque Partie pourra y mettre fin, moyennant la notification de sa volonté à l'autre Partie par simple e-mail outre le respect d'un délai de préavis d'un mois.<br><br>
        Dès la conclusion du contrat s'ouvrira une période d'essai correspondant à deux réservations. Pendant cette période, chaque partie pourra mettre fin au contrat sans observer de délai de préavis et sans justification particulière, moyennant la notification de sa volonté à l'autre partie par e-mail.
    </p><br>

    <p><strong>Article 8 – Clause de résiliation</strong><br><br>
        Si l'une ou l'autre des Parties venait à manquer à l'une ou l'autre de ses obligations et si ledit manquement venait à persister à l'expiration d'un délai de 30 (trente) jours suivant plusieurs avertissements par email ou message sans effets, le présent contrat pourra être résilié avec effet immédiat, tous dommages-intérêts éventuels demeurant réservés.
    </p><br>

    <p><strong>Article 9 – Règlement des litiges</strong><br><br>
        Au cas où un différend surviendrait entre les Parties dans l'exécution ou l'interprétation de la présente convention, les Parties s'obligent à tenter de le résoudre préalablement de façon amiable.<br><br>
        Si au terme d'un délai de 30 jours les Parties n'arrivent pas à se mettre d'accord, le différend sera alors soumis aux tribunaux compétents.
    </p><br>

    <p><strong>Article 10 – Loi applicable</strong><br><br>
        La présente convention est régie par les lois et règlements de la République de Côte d'Ivoire.
    </p><br>

    <p><strong>Article 11 – Élection de domicile</strong><br><br>
        Pour l'exécution des présentes, ainsi que de leurs suites, les Parties font élection de domicile en leurs sièges sociaux indiqués en tête des présentes.
    </p><br>
    {{-- <p><strong>Article 7 – Durée du contrat</strong><br><br>
        Le présent contrat est conclu pour une durée de six mois, reconductible tacitement. Chaque Partie pourra y
        mettre fin, moyennant la notification de sa volonté à l’autre Partie par simple e-mail, outre le respect d’un
        délai de préavis d’un mois.<br>
        Dès la conclusion du contrat s’ouvrira une période d’essai correspondant à deux réservations. Pendant cette
        période, chaque partie pourra mettre fin au contrat sans observer de délai de préavis et sans justification
        particulière, moyennant la notification de sa volonté à l’autre partie par écrit.
        </p><br>

        <p><strong>Article 8 – Clause de résiliation</strong><br>
            Si l’une ou l’autre des Parties venait à manquer à l’une ou l’autre de ses obligations et si ledit manquement
            venait à persister à l’expiration d’un délai de 30 jours suivant plusieurs avertissements par email ou message
            sans effet, le présent contrat pourra être résilié avec effet immédiat, tous dommages-intérêts éventuels
            demeurant réservés.
        </p><br>

        <p><strong>Article 9 – Règlement des litiges</strong><br><br>
            Au cas où un différend surviendrait entre les Parties dans l’exécution ou l’interprétation de la présente
            convention, les Parties s’obligent à tenter de le résoudre préalablement de façon amiable.<br>
            Si au terme d’un délai de 30 jours les Parties n’arrivent pas à se mettre d’accord, le différend sera alors
            soumis aux tribunaux compétents.
        </p><br>

        <p><strong>Article 10 – Loi applicable</strong><br><br>
            La présente convention est régie par les lois et règlements de la République de Côte d’Ivoire.</p><br>

        <p><strong>Article 11 – Élection de domicile</strong><br><br>
            Pour l’exécution des présentes, ainsi que de leurs suites, les Parties font élection de domicile en leurs sièges
            sociaux indiqués en tête des présentes.
        </p><br> 
    --}}

    <p>Fait à Abidjan, le {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('l d F Y') }} en deux (2)
        exemplaires originaux</p>

    <div class="signature" style="height: 60vh;">
        <div style="height: 45vh; float: left">
            <p style="text-align: left">La structure d’hébergement</p>
            <p>
                
            </p>
        </div>
        <div style="height: 45vh; float: right">
            <p style="text-align: right">La société gérante</p>
            <p style="text-align: right">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo/sign-jsbey.jpeg'))) }}"
                    style="width: 70%" alt="MOKAZ signature" class="log">
            </p>
        </div>
    </div>
    <div style="clear: both"></div>

    <div class="footer" style="width: 100%; position: fixed; bottom: 0;">
        <p style="text-align: center; margin: 0; font-size: 12px; font-weight: normal; font-size: 15px;">JSBEY – Société
            à responsabilité limitée – RCCM
            CI-ABJ-03-2024-B13-01106<br>
            Cité Alliance Akwaba Anyama – Lot 317 – IM 17 – n°13 – 11 BP 1713 Abidjan 11<br>
            Tél : 0787245197 – 0767964144</p>
    </div>

</body>

</html>
