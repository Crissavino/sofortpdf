<?php

return [
    'accepted' => ':attribute muss akzeptiert werden.',
    'between' => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file' => ':attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string' => ':attribute muss zwischen :min und :max Zeichen lang sein.',
        'array' => ':attribute muss zwischen :min und :max Elemente haben.',
    ],
    'confirmed' => ':attribute stimmt nicht mit der Bestätigung überein.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'max' => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file' => ':attribute darf nicht größer als :max Kilobytes sein.',
        'string' => ':attribute darf nicht länger als :max Zeichen sein.',
    ],
    'min' => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'required' => ':attribute ist erforderlich.',
    'string' => ':attribute muss ein String sein.',
    'unique' => ':attribute ist bereits vergeben.',

    'attributes' => [
        'email' => 'E-Mail-Adresse',
        'password' => 'Passwort',
        'name' => 'Name',
        'password_confirmation' => 'Passwort-Bestätigung',
    ],
];
