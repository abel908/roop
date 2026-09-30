<?php

return [
    'hero' => [
        'eyebrow' => 'Submit a Project',
        'title' => 'Present your project to investors',
        'lead' => 'Complete the three steps of the form. Our team reviews every file and contacts you with the next steps.',
    ],
    'aside' => [
        'title' => 'Before you start',
        'items' => [
            'About 10 minutes to fill in the form.',
            'Prepare your documents: business plan, financial model, presentation.',
            'Your file is confidential and stored in a private space.',
        ],
        'help' => 'A question? Contact us',
    ],
    'progress' => 'Step :current of :total',
    'steps' => [
        1 => ['title' => 'Project holder', 'text' => 'Tell us who you are.'],
        2 => ['title' => 'Project', 'text' => 'Describe your project and your need.'],
        3 => ['title' => 'Documents & validation', 'text' => 'Add your documents and review your file.'],
    ],
    'fields' => [
        'project_name' => 'Project Name',
        'project_country' => 'Country (location of the project)',
        'sector' => 'Sector',
        'description' => 'Description',
        'description_help' => 'Market, business model, team, current situation (min. 50 characters).',
        'stage' => 'Development Stage',
        'investment' => 'Investment Required',
        'currency' => 'Currency',
        'funding' => 'Funding Need',
        'timeline' => 'Timeline',
        'timeline_help' => 'Desired date or duration, e.g. “Q2 2027” or “within 12 months”.',
        'documents' => 'Documents',
        'documents_help' => 'PDF, Word, Excel, PowerPoint, JPG or PNG — up to :count files of :size MB each. Recommended.',
        'drop' => 'Choose files',
        'drop_text' => 'or drag them here, from your computer or phone',
        'remove' => 'Remove',
        'consent' => 'I have read and accept the :policy.',
        'certify' => 'I certify that the information provided is accurate and that I am authorised to present this project.',
    ],
    'summary' => [
        'title' => 'Review before sending',
        'holder' => 'Project holder',
        'project' => 'Project',
        'documents' => 'Documents',
        'no_documents' => 'No document attached',
        'edit' => 'Edit',
    ],
    'actions' => [
        'next' => 'Continue',
        'previous' => 'Back',
        'submit' => 'Submit my project',
    ],
    'reupload' => 'For security reasons, attached files must be selected again after a correction.',
];
