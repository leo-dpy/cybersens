<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');



$major_incidents = [
    [
        'title' => 'Kiabi',
        'description' => 'Fuite des IBAN de 20 000 clients via une attaque par Credential Stuffing.',
        'date' => '2026-01-07', 
        'source' => 'Fuite Bancaire',
        'link' => 'https://www.kiabi.com'
    ],
    [
        'title' => 'Mondial Relay',
        'description' => 'Vol de données personnelles et détails de livraison touchant des millions de clients.',
        'date' => '2025-12-23',
        'source' => 'Vol de Données',
        'link' => '#'
    ],
    [
        'title' => 'La Poste & Banque Postale',
        'description' => 'Attaque DDoS massive rendant les services inaccessibles juste avant Noël.',
        'date' => '2025-12-22',
        'source' => 'Paralysie',
        'link' => '#'
    ],
    [
        'title' => 'Pass\'Sport / Ministère des Sports',
        'description' => 'Exfiltration de données de 3,5 millions de foyers (Identités, Sécu, IBAN).',
        'date' => '2025-12-19',
        'source' => 'Fuite Massive',
        'link' => '#'
    ],
    [
        'title' => 'Ministère de l\'Intérieur',
        'description' => 'Intrusion serveurs messagerie, accès fichiers police sensibles (TAJ, FPR).',
        'date' => '2025-12-11',
        'source' => 'Intrusion Critique',
        'link' => '#'
    ],
    [
        'title' => 'MédecinDirect',
        'description' => 'Violation de données de santé très sensibles (motifs consultation, échanges médicaux).',
        'date' => '2025-12-05',
        'source' => 'Données Santé',
        'link' => '#'
    ],
    [
        'title' => 'Missions Locales',
        'description' => 'Fuite impactant 1,6 million de jeunes suivis par le réseau.',
        'date' => '2025-12-01',
        'source' => 'Données Sociales',
        'link' => '#'
    ],
    [
        'title' => 'Fédération Française de Football',
        'description' => 'Troisième cyberattaque en deux ans, touchant les données des licenciés.',
        'date' => '2025-11-26',
        'source' => 'Piratage',
        'link' => '#'
    ],
    [
        'title' => 'Colis Privé',
        'description' => 'Compromission des données de contact de millions de clients (risque phishing).',
        'date' => '2025-11-21',
        'source' => 'Fuite Clients',
        'link' => '#'
    ],
    [
        'title' => 'Pajemploi / URSSAF',
        'description' => 'Vol de données touchant 1,2 million d\'usagers (employeurs/salariés).',
        'date' => '2025-11-14',
        'source' => 'Fuite Admin',
        'link' => '#'
    ],
    [
        'title' => 'Eurofiber France',
        'description' => 'Attaque critique infrastructure, données de 3600 organisations exposées (SNCF, Airbus...).',
        'date' => '2025-11-13',
        'source' => 'Infrastructure',
        'link' => '#'
    ],
    [
        'title' => 'France Travail',
        'description' => 'Nouvelle compromission ciblant 31 000 comptes via infostealers.',
        'date' => '2025-10-27',
        'source' => 'Piratage Compte',
        'link' => '#'
    ],
    [
        'title' => 'Lycées publics Hauts-de-France',
        'description' => 'Ransomware Qilin paralysant 60 000 ordinateurs (80% des lycées) et vol données.',
        'date' => '2025-10-10',
        'source' => 'Rançongiciel',
        'link' => '#'
    ],
    [
        'title' => 'Hôpitaux publics Hauts-de-France',
        'description' => 'Attaque visant les serveurs d\'identité des patients, retour au papier.',
        'date' => '2025-09-08',
        'source' => 'Hôpital',
        'link' => '#'
    ],
    [
        'title' => 'Auchan',
        'description' => 'Cyberattaque ciblant les comptes de fidélité (cagnottes, historiques d\'achat).',
        'date' => '2025-08-21',
        'source' => 'Commerce',
        'link' => '#'
    ],
    [
        'title' => 'Bouygues Telecom',
        'description' => 'Fuite massive 6,4 millions de clients (État civil, IBAN, Coordonnées).',
        'date' => '2025-08-06',
        'source' => 'Fuite Massive',
        'link' => '#'
    ],
    [
        'title' => 'Air France-KLM',
        'description' => 'Fuite de données via prestataire Salesforce, membres Flying Blue touchés.',
        'date' => '2025-08-06',
        'source' => 'Supply Chain',
        'link' => '#'
    ],
    [
        'title' => 'Sorbonne Université',
        'description' => 'Vol de données de 32 000 étudiants et employés.',
        'date' => '2025-06-16',
        'source' => 'Université',
        'link' => '#'
    ],
    [
        'title' => 'Disneyland Paris',
        'description' => 'Revendication de vol de 64 Go de données confidentielles par le groupe Anubis.',
        'date' => '2025-06-20',
        'source' => 'Vol de Données',
        'link' => '#'
    ],
    [
        'title' => 'Reduction-Impots.fr',
        'description' => 'Vente sur dark web de données fiscales de 2 millions de Français.',
        'date' => '2025-05-14',
        'source' => 'Dark Web',
        'link' => '#'
    ]
];

$news = [];

// Construction de la liste finale pour le JSON
foreach ($major_incidents as $inc) {
    $news[] = [
        'title' => $inc['title'],
        'link' => $inc['link'],
        'description' => $inc['description'],
        'date' => strtotime($inc['date']),
        'source' => $inc['source'],
        'image' => null, // Pas d'image nécessaire pour le style terminal
        'is_attack' => true
    ];
}

// Trier par date décroissante (plus récent en haut)
usort($news, function($a, $b) {
    return $b['date'] - $a['date'];
});

echo json_encode(['success' => true, 'news' => $news]);
?>