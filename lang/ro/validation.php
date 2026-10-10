<?php
return [
    'required' => 'Câmpul :attribute este obligatoriu.',
    'email' => 'Câmpul :attribute trebuie să fie o adresă de e-mail validă.',
    'min' => ['string' => 'Câmpul :attribute trebuie să aibă cel puțin :min caractere.'],
    'max' => ['string' => 'Câmpul :attribute poate avea cel mult :max caractere.', 'file' => 'Fișierul poate avea cel mult :max KB.'],
    'confirmed' => 'Confirmarea câmpului :attribute nu se potrivește.',
    'unique' => 'Această valoare pentru :attribute este deja folosită.',
    'accepted' => 'Câmpul :attribute trebuie acceptat.',
    'date' => 'Câmpul :attribute nu este o dată validă.',
    'current_password' => 'Parola nu este corectă.',
    'image' => 'Fișierul trebuie să fie o imagine.',
    'attributes' => [
        'name' => 'nume', 'email' => 'e-mail', 'password' => 'parolă', 'phone' => 'telefon',
        'resident_name' => 'numele beneficiarului', 'consent' => 'acordul',
    ],
];
