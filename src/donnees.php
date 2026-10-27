<?php
require_once __DIR__ . '/Model/Livre.php';

// Tableau d'objets : remplace les tableaux associatifs du TP 2.
$livres = [
    new Livre(1, '1984', 'George Orwell', 1949, 12.50),
    new Livre(2, 'Germinal', 'Émile Zola', 1885, 9.90),
    new Livre(3, 'Les Misérables', 'Victor Hugo', 1862, 15.00),
    new Livre(4, 'Dune', 'Frank Herbert', 1965, 11.20),
    new Livre(5, 'Fondation', 'Isaac Asimov', 1951, 10.00),
];