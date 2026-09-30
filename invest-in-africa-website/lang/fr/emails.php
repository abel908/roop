<?php

// Emails transactionnels (§6.5) — modifiables dans le back-office. Variables : :reference, :name, :project…
return [
    'footer' => 'Ce message a été envoyé automatiquement par The Invest In Africa Initiative. Merci de ne pas y répondre directement si aucune adresse de réponse n’est indiquée.',
    'details' => 'Récapitulatif',
    'greeting' => 'Bonjour :name,',
    'signature' => 'L’équipe de The Invest In Africa Initiative',

    'submission_received' => [
        'subject' => 'Votre projet a bien été reçu — :reference',
        'heading' => 'Votre projet a bien été reçu',
        'body' => 'Merci d’avoir soumis votre projet « :project ». Votre numéro de dossier est :reference.',
        'next' => 'Notre équipe va examiner votre dossier et pourra vous contacter pour obtenir des informations complémentaires. Conservez votre numéro de dossier pour tous vos échanges.',
    ],
    'submission_team' => [
        'subject' => 'Nouveau projet soumis — :reference',
        'heading' => 'Nouveau projet soumis',
        'body' => 'Un nouveau projet « :project » a été soumis par :name.',
        'action' => 'Ouvrir le dossier dans le back-office',
    ],
    'submission_status' => [
        'subject' => 'Mise à jour de votre dossier :reference',
        'heading' => 'Votre dossier a été mis à jour',
        'body' => 'Le statut de votre projet « :project » (:reference) est désormais : :status.',
        'next' => 'Notre équipe reste à votre disposition pour toute question.',
    ],
    'interest_received' => [
        'subject' => 'Votre manifestation d’intérêt — :project_reference',
        'heading' => 'Votre manifestation d’intérêt a bien été reçue',
        'body' => 'Merci pour votre intérêt pour le projet « :project » (:project_reference). Votre numéro de dossier est :reference.',
        'next' => 'Un chargé de projets vous contactera prochainement pour convenir de la suite.',
        'action' => 'Voir le projet',
    ],
    'interest_team' => [
        'subject' => 'Nouvelle manifestation d’intérêt — :project_reference',
        'heading' => 'Nouvelle manifestation d’intérêt',
        'body' => ':name a manifesté son intérêt pour le projet :project_reference.',
        'action' => 'Ouvrir dans le back-office',
    ],
    'contact_received' => [
        'subject' => 'Votre message a bien été reçu — :reference',
        'heading' => 'Votre message a bien été reçu',
        'body' => 'Merci de nous avoir contactés. Votre message a été transmis à l’équipe concernée sous le numéro :reference.',
        'next' => 'Nous vous répondrons dans les meilleurs délais.',
    ],
    'contact_team' => [
        'subject' => 'Nouveau message (:subject) — :reference',
        'heading' => 'Nouveau message de contact',
        'body' => 'Un nouveau message a été envoyé par :name.',
        'action' => 'Ouvrir dans le back-office',
    ],
];
