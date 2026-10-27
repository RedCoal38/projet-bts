<?php
class Livre
{
    private int $id;
    private string $titre;
    private string $auteur;
    private int $annee;
    private float $prix;

    public function __construct(int $id, string $titre, string $auteur, int $annee, float $prix)
    {
        $this->id = $id;
        $this->setTitre($titre);
        $this->setAuteur($auteur);
        $this->setAnnee($annee);
        $this->setPrix($prix);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        if (trim($titre) === '') {
            throw new InvalidArgumentException('Le titre ne peut pas être vide.');
        }
        $this->titre = $titre;
    }

    public function getAuteur(): string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): void
    {
        if (trim($auteur) === '') {
            throw new InvalidArgumentException('L\'auteur ne peut pas être vide.');
        }
        $this->auteur = $auteur;
    }

    public function getAnnee(): int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): void
    {
        if ($annee <= 0) {
            throw new InvalidArgumentException('L’année doit être positive.');
        }
        $this->annee = $annee;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): void
    {
        if ($prix < 0) {
            throw new InvalidArgumentException('Le prix ne peut pas être négatif.');
        }
        $this->prix = $prix;
    }

    public function getDescription(): string
    {
        return $this->titre . ' (' . $this->annee . ')';
    }
}