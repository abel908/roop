<?php

// Transactional emails (§6.5) — editable in the back-office. Placeholders: :reference, :name, :project…
return [
    'footer' => 'This message was sent automatically by The Invest In Africa Initiative. Please do not reply directly if no reply address is indicated.',
    'details' => 'Summary',
    'greeting' => 'Hello :name,',
    'signature' => 'The team of The Invest In Africa Initiative',

    'submission_received' => [
        'subject' => 'Your project has been received — :reference',
        'heading' => 'Your project has been received',
        'body' => 'Thank you for submitting your project “:project”. Your file number is :reference.',
        'next' => 'Our team will review your file and may contact you for additional information. Please keep your file number for all future exchanges.',
    ],
    'submission_team' => [
        'subject' => 'New project submitted — :reference',
        'heading' => 'New project submitted',
        'body' => 'A new project “:project” has been submitted by :name.',
        'action' => 'Open the file in the back-office',
    ],
    'submission_status' => [
        'subject' => 'Update on your file :reference',
        'heading' => 'Your file has been updated',
        'body' => 'The status of your project “:project” (:reference) is now: :status.',
        'next' => 'Our team remains at your disposal for any question.',
    ],
    'interest_received' => [
        'subject' => 'Your expression of interest — :project_reference',
        'heading' => 'Your expression of interest has been received',
        'body' => 'Thank you for your interest in project “:project” (:project_reference). Your file number is :reference.',
        'next' => 'A project officer will contact you shortly to discuss the next steps.',
        'action' => 'View the project',
    ],
    'interest_team' => [
        'subject' => 'New expression of interest — :project_reference',
        'heading' => 'New expression of interest',
        'body' => ':name has expressed interest in project :project_reference.',
        'action' => 'Open in the back-office',
    ],
    'contact_received' => [
        'subject' => 'Your message has been received — :reference',
        'heading' => 'Your message has been received',
        'body' => 'Thank you for contacting us. Your message has been forwarded to the relevant team under number :reference.',
        'next' => 'We will reply as soon as possible.',
    ],
    'contact_team' => [
        'subject' => 'New message (:subject) — :reference',
        'heading' => 'New contact message',
        'body' => 'A new message has been sent by :name.',
        'action' => 'Open in the back-office',
    ],
];
