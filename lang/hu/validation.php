<?php
return [
    'required' => 'A(z) :attribute mező kitöltése kötelező.',
    'email' => 'A(z) :attribute mezőbe érvényes e-mail-címet adjon meg.',
    'min' => ['string' => 'A(z) :attribute legalább :min karakter legyen.'],
    'max' => ['string' => 'A(z) :attribute legfeljebb :max karakter lehet.', 'file' => 'A fájl legfeljebb :max KB lehet.'],
    'confirmed' => 'A(z) :attribute megerősítése nem egyezik.',
    'unique' => 'Ez a(z) :attribute már foglalt.',
    'accepted' => 'A(z) :attribute elfogadása kötelező.',
    'date' => 'A(z) :attribute nem érvényes dátum.',
    'current_password' => 'A jelszó nem helyes.',
    'image' => 'A fájlnak képnek kell lennie.',
    'attributes' => [
        'name' => 'név', 'email' => 'e-mail', 'password' => 'jelszó', 'phone' => 'telefonszám',
        'resident_name' => 'lakó neve', 'consent' => 'hozzájárulás',
        'representative' => 'képviselő adatai', 'resident' => 'beköltöző adatai',
    ],
];
