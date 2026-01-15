# Project Requirement Document (PRD)

**Project Name:** Mbata  
**Goal:** Créer une plateforme professionnelle inspirée d’Airbnb, adaptée au marché algérien, disponible en site web et en PWA, multilingue avec le français comme langue principale, supportant l’arabe et l’anglais, et permettant la location de logements et espaces avec paiements locaux et internationaux sous gestion complète du super admin.

## Core Features (MVP)

1. Plateforme de Location
   - Publication et consultation de logements et espaces à louer
   - Locations courte et moyenne durée
   - Gestion des disponibilités et des prix
   - Recherche par wilaya, ville, dates et type de logement
   - Interface simple respectant la culture algérienne

2. Espace Propriétaire
   - Inscription et gestion de compte
   - Ajout et gestion des logements
   - Gestion des réservations
   - Suivi des revenus
   - Accès limité aux données sensibles

3. Espace Client
   - Recherche et consultation des logements
   - Réservation en ligne
   - Paiement sécurisé
   - Gestion du profil utilisateur
   - Historique des réservations
   - Contact avec les propriétaires

4. Système de Paiement
   - Devise interne principale: Dinar Algérien
   - Affichage des prix en DZD, EUR ou USD
   - Taux de conversion configurables par le super admin
   - Paiement via Baridi CCP
     - Affichage dynamique des informations de virement
     - Téléversement obligatoire du justificatif de paiement
     - Validation manuelle par l’administration
   - Paiement via Stripe
     - Paiement par carte bancaire
     - Confirmation automatique du paiement

5. Section Promotion Immobilière Admin
   - Section dédiée au super admin et aux administrateurs
   - Publication de biens à vendre à titre publicitaire
   - Aucune réservation ni paiement
   - Fiche de présentation avec formulaire de contact
   - Objectif marketing uniquement

6. Authentification et Comptes
   - Inscription par email
   - Inscription par numéro de téléphone
   - Vérification par email ou SMS
   - Connexion sécurisée
   - Gestion des rôles et permissions

7. Administration
   - Super Admin Dashboard
     - Gestion des administrateurs
     - Gestion des taux de change
     - Gestion globale des utilisateurs
     - Gestion des paiements et validations
   - Admin Dashboard
     - Gestion des clients et propriétaires
     - Modération des annonces
     - Gestion des réservations
   - Accès aux justificatifs Baridi CCP

8. Localisation Algérienne
   - Intégration complète des wilayas et communes d’Algérie
   - Données préchargées depuis le référentiel officiel
   - Sélection simple par listes déroulantes
   - Conformité avec les données locales

9. Logements et Caractéristiques
   - Types de logements
     - Appartement
     - Maison
     - Studio
     - Chambre
   - Catégories
     - Famille
     - Voyage d’affaires
     - Vacances
   - Équipements
     - Eau, gaz, électricité
     - WiFi
     - Climatisation
     - Chauffage
     - Parking
   - Galerie photos et descriptions détaillées

10. Web et PWA
    - Site web complet accessible sur desktop et mobile
    - Version PWA avec toutes les fonctionnalités
      - Installation sur écran d’accueil
      - Mode hors ligne partiel
      - Mise en cache intelligente
      - Notifications push
      - Performances optimisées mobile
    - Architecture prête pour conversion future en APK

11. Multilingue
    - Langue principale: Français
    - Langues secondaires: Arabe et Anglais
    - Sélecteur de langue accessible sur l’interface
    - Support RTL pour la langue arabe
    - Contenus administrables dans toutes les langues

12. Design et Expérience Utilisateur
    - Couleur principale orange
    - Design moderne et chaleureux
    - UX simple et intuitive
    - Responsive desktop et mobile
    - Respect des standards culturels algériens

## Tech Stack Rules

- Framework: Laravel 12 avec strict types
- Frontend: Vue 3 avec Inertia.js en Composition API
- Styling: TailwindCSS
- Database: MySQL

## Data Sources

- Wilayas et communes d’Algérie
- Repository officiel: https://github.com/othmanus/algeria-cities.git

## Non Functional Requirements

- Sécurité élevée des données et paiements
- Performance optimisée pour web et PWA
- Architecture scalable et maintenable
- Code propre et documenté
- Respect des bonnes pratiques Laravel et Vue

## Out of Scope (Phase MVP)

- Application mobile native
- Messagerie temps réel avancée
- Programmes de fidélité
