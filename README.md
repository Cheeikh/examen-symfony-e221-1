# Clinique 221 - Système de Gestion des Rendez-vous

Application web de gestion des rendez-vous pour la Clinique 221, permettant la prise de rendez-vous pour des consultations médicales et des prestations (analyses, radiologie, etc.).

## Prérequis

- PHP 8.0 ou supérieur
- Composer
- MySQL 5.7 ou supérieur
- Symfony CLI (recommandé pour le développement)

## Installation

1. Cloner le dépôt:
   ```bash
   git clone [url-du-dépôt]
   cd exam-symfony
   ```

2. Installer les dépendances:
   ```bash
   composer install
   ```

3. Configurer la base de données dans le fichier `.env`:
   ```
   DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
   ```

4. Créer la base de données et exécuter les migrations:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. Charger les fixtures (données de test):
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Démarrage du serveur

```bash
symfony server:start
```
ou
```bash
php -S localhost:8000 -t public/
```

L'application sera accessible à l'adresse: http://localhost:8000

## Comptes de test

### Patient
- Email: `patient1@example.com`
- Mot de passe: `password`

<!-- ### Médecin
- Email: `medecin1@example.com`
- Mot de passe: `password`

### Secrétaire
- Email: `secretaire@example.com`
- Mot de passe: `password`

### Responsable des prestations
- Email: `responsable@example.com`
- Mot de passe: `password` -->

## Structure de l'application

### Entités principales
- `Patient`: Utilisateur pouvant prendre rendez-vous
<!-- - `Medecin`: Professionnel de santé réalisant les consultations
- `RendezVous`: Demande de consultation ou prestation
- `Consultation`: Session médicale avec un médecin
- `Prestation`: Service médical (analyse, radiologie, etc.)
- `Resultat`: Résultat d'une prestation -->

### Contrôleurs
- `SecurityController`: Gestion de l'authentification
- `RegistrationController`: Inscription des nouveaux utilisateurs
- `HomeController`: Page d'accueil
- `PatientController`: Fonctionnalités pour les patients
- `ContactController`: Formulaire de contact

## Fonctionnalités Patient

- Tableau de bord (vue d'ensemble)
- Gestion des rendez-vous (consultation et prestation)
- Prise de nouveau rendez-vous
- Annulation de rendez-vous (48h avant minimum)
- Consultation du dossier médical
- Consultation des résultats
- Messagerie (communication avec les médecins et services)

## Configuration

### Email (développement)
Pour tester l'envoi d'emails en développement, configurez le DSN dans `.env`:
```
MAILER_DSN=smtp://localhost:1025
```

Pour utiliser Mailhog ou MailDev:
```bash
# Avec Mailhog
docker run -p 1025:1025 -p 8025:8025 mailhog/mailhog

# Interface web: http://localhost:8025
```

### CSRF Protection
La protection CSRF est activée pour tous les formulaires. Les tokens CSRF sont générés automatiquement.

### Sessions
Les sessions sont configurées en mode strict avec un délai d'expiration standard.

## Développement

### Commandes utiles

- Vider le cache: `php bin/console cache:clear`
- Créer une entité: `php bin/console make:entity`
- Créer un contrôleur: `php bin/console make:controller`
- Créer un formulaire: `php bin/console make:form`
- Créer une migration: `php bin/console make:migration`
- Exécuter les tests: `php bin/phpunit` 