#!/usr/bin/env bash
set -e

LOGIN=$(whoami)
BASE="$HOME/projets"
WEB="$HOME/public_html"

read -rp "Nom du projet : " PROJET
if [[ -z "$PROJET" || "$PROJET" == '.' || "$PROJET" == '..' || "$PROJET" == *'/'* ]]; then
  echo "Erreur : indiquez un nom de dossier valide." >&2
  exit 1
fi

SOURCE="$WEB/$PROJET"
DESTINATION="$BASE/$PROJET"
LIEN="$WEB/$PROJET"
CIBLE="$DESTINATION/public"

if [[ ! -d "$DESTINATION" ]]; then
  if [[ ! -d "$SOURCE" || -L "$SOURCE" ]]; then
    echo "Projet introuvable dans $DESTINATION ou $SOURCE" >&2
    exit 1
  fi
  if [[ ! -d "$SOURCE/public" ]]; then
    echo "Dossier public/ manquant dans $SOURCE" >&2
    exit 1
  fi
  mkdir -p "$BASE"
  echo "Déplacement prévu : $SOURCE -> $DESTINATION"
  read -rp "Confirmer ? (o/N) : " REP
  [[ "$REP" =~ ^[oO]$ ]] || exit 0
  mv -- "$SOURCE" "$DESTINATION"
  echo "Projet déplacé."
fi

if [[ ! -d "$CIBLE" ]]; then
  echo "Dossier public/ manquant dans $DESTINATION" >&2
  exit 1
fi

mkdir -p "$WEB"
chmod o+x "$HOME" "$BASE" "$DESTINATION"
chmod o+rx "$CIBLE"

if [[ -L "$LIEN" ]]; then
  if [[ "$(readlink -- "$LIEN")" == "$CIBLE" ]]; then
    echo "Lien symbolique déjà correct : $LIEN"
  else
    echo "Un lien symbolique différent existe : $LIEN -> $(readlink -- "$LIEN")" >&2
    exit 1
  fi
elif [[ -e "$LIEN" ]]; then
  echo "Le chemin $LIEN existe déjà et n'est pas un lien symbolique. Aucune modification." >&2
  exit 1
else
  ln -s -- "$CIBLE" "$LIEN"
  echo "Lien créé : $LIEN -> $CIBLE"
fi

echo "Projet : $DESTINATION"
echo "URL : http://localhost/~$LOGIN/$PROJET/"
