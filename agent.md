# Guide pour les assistants IA — kasa-app

Ce fichier s'adresse aux assistants de programmation (Copilot, Cursor, Claude, ChatGPT ou tout autre outil intégré à l'éditeur) qui interviennent sur le dépôt `kasa-app`. Il décrit la posture attendue, le projet et sa stack, et les règles à respecter avant de proposer du code.

## Posture attendue

- **Expliquer avant de montrer.** Quand une question est posée, commencez par le raisonnement : quel est le problème, quelles sont les options, laquelle vous paraît adaptée et pourquoi. Le code vient ensuite, en appui de l'explication.
- **Procéder par petites étapes.** Un changement à la fois, vérifiable (l'API répond, l'interface se lance, `composer lint`, `npm run lint` et `npm run build` passent) avant de passer au suivant. Pas de refonte en bloc.
- **Poser des questions avant de coder.** Si le périmètre, le comportement attendu ou l'emplacement d'un changement sont ambigus, demandez. Une hypothèse non dite coûte plus cher qu'une question.
- **Laisser la décision à la personne.** Proposez, comparez, argumentez ; ne tranchez pas à sa place sur l'architecture, les dépendances ou les mécanismes à mettre en place. Elle doit pouvoir justifier chaque choix sans vous.
- **Rester dans les conventions du dépôt.** Noms en français, PSR-12 et `declare(strict_types=1)` côté PHP, composants fonctions et hooks côté React, appels HTTP dans `frontend/src/lib/api.ts`, session dans `frontend/src/lib/session.ts`.

## Le projet

`kasa-app` est la plateforme de réservation de logements entre particuliers de Kasa. Deux applications qui ne partagent qu'un contrat HTTP (routes, JSON, codes de réponse) :

- `backend/` — API **Symfony 7.4** sur **PHP 8.5.11**, sans base de données : le service `App\Donnees\ChargeurCsv` lit le fichier CSV désigné par `DATA_FILE` (dans `starter-kit/`).
  - `src/Controller/` : `SanteController` (`GET /api/sante`), `LogementController` (`GET /api/logements`, `GET /api/logements/{id}`), `AuthController` (`POST /api/auth/login`). Routes déclarées par attributs `#[Route]`.
  - `src/Donnees/` : objets valeur `readonly` `Utilisateur`, `Logement`, `JeuDeDonnees`, et le service `ChargeurCsv`.
  - `config/` : `services.yaml` (autowiring de `App\`, injection du chemin du CSV), `packages/nelmio_cors.yaml` (origine de l'interface), `routes.yaml`.
- `frontend/` — interface **React 19.3.0** construite avec **Vite 7**, TypeScript strict, **React Router 7**, exécutée avec **Node.js 24.21.0** pour l'outillage.
  - `src/pages/` : `Accueil` (liste), `DetailLogement` (détail), `Connexion`.
  - `src/components/` : `Header`, `CarteLogement`, `FormulaireConnexion`.
  - `src/lib/` : `types.ts` (types partagés), `api.ts` (client HTTP vers l'API), `session.ts` (session dans le navigateur, lue avec `useSyncExternalStore`).
- `starter-kit/` — documents du projet et jeu de données CSV lu par l'API.

Commandes : `composer install` puis `symfony server:start --port=8000` dans `backend/` ; `npm install` puis `npm run dev` (port 5173) dans `frontend/` ; `composer lint`, `npm run lint`, `npm run build`. Débogage : configurations Xdebug et Chrome dans `.vscode/launch.json`.

## Bonnes pratiques de la stack à rappeler

### PHP et Symfony

- Chaque fichier commence par `declare(strict_types=1);` ; les paramètres, retours et propriétés sont typés ; les classes sont chargées par l'autoload PSR-4 (`App\` → `src/`). Pas de `require` à la main.
- Les contrôleurs restent minces : lire la requête, appeler un service, rendre une `JsonResponse`. La logique vit dans des services reçus par le constructeur (autowiring).
- Ce qui sort de l'API se choisit explicitement, champ par champ : on ne renvoie jamais un objet entier parce que c'est plus court.
- Les entrées se vérifient sur le serveur ; ce que l'interface vérifie avant l'envoi est un confort pour l'utilisateur, pas une protection.
- Les erreurs renvoyées au client restent génériques ; le détail va dans les journaux (`var/log/`). `APP_ENV=prod` en production, jamais `dev`.
- Le CORS n'autorise que l'origine de l'interface (`CORS_ALLOW_ORIGIN`). On ne l'ouvre pas à `*` pour faire taire une erreur du navigateur.
- Si une base de données est introduite un jour, les requêtes passent par Doctrine ou par des requêtes préparées, jamais par concaténation. Un mot de passe se hache avec `password_hash` ou le hacheur de Symfony, jamais avec `md5` ni `sha1`.
- La configuration non secrète est dans `.env` versionné ; tout ce qui est secret ou propre à une machine va dans `.env.local`, ignoré par Git.

### React, Vite et TypeScript

- Composants fonctions et hooks ; l'état au plus près de qui s'en sert. Ce qui se calcule à partir de l'état se calcule au rendu, on ne le stocke pas une seconde fois.
- `useEffect` sert à se synchroniser avec l'extérieur (un appel réseau, un abonnement). Un effet qui lance une requête vérifie qu'il est encore actif avant de mettre à jour l'état, comme le font les pages de ce dépôt. Pour lire une source externe comme le `localStorage`, `useSyncExternalStore`, comme le fait `Header`.
- Les appels à l'API passent par `src/lib/api.ts`, pas par des `fetch` éparpillés dans les composants.
- Les clés de liste sont des identifiants stables (`logement.id`), jamais l'index du tableau.
- Mode strict TypeScript : props et réponses d'API typées. Un `any` se justifie par écrit ; un `unknown` suivi d'une vérification est presque toujours préférable. Les types partagés vivent dans `src/lib/types.ts`.
- Les variables `VITE_*` sont lues via `import.meta.env` et typées dans `src/vite-env.d.ts` ; elles partent dans le navigateur.

### Tests, quand il y en a

- Côté PHP : PHPUnit, en tests unitaires sur les services et en tests fonctionnels d'API (`WebTestCase`) ; une configuration `.env.test` dédiée, jamais les données de développement.
- Côté interface : on teste le comportement visible (`getByRole`, `getByLabelText`), pas l'implémentation (état interne, classe CSS).
- Les parcours de bout en bout attendent un état de la page ou une requête interceptée (`cy.intercept` puis `cy.wait('@alias')`), jamais un délai fixe.

## Secrets et données

- **Jamais de secret dans le code**, ni dans un commit, ni dans une variable `VITE_*` : tout ce qui porte ce préfixe est envoyé au navigateur et lisible par tous. Les secrets vont dans `backend/.env.local` et `frontend/.env.local`, ignorés par Git ; `backend/.env` et `frontend/.env.example` sont versionnés avec des valeurs vides ou factices.
- **Les comptes du fichier de données sont des données personnelles** (prénom, nom, adresse e-mail, mot de passe). Ne les recopiez pas dans des exemples, des tests, des messages de commit ou des réponses ; référencez-les par leur identifiant (`u-006`) quand c'est nécessaire.
- **Ne faites pas confiance au code généré.** Relisez-le, exécutez-le, vérifiez qu'il passe `composer lint`, `npm run lint` et `npm run build`, et qu'il fait ce qui était demandé. Signalez vous-même ce dont vous n'êtes pas sûr.
- **Ne proposez pas d'ajouter une dépendance** sans dire ce qu'elle apporte, ce qu'elle pèse et comment elle est maintenue. Vérifiez la compatibilité avec PHP 8.5, Symfony 7.4, React 19 et Node.js 24, et respectez la ligne `7.4.*` pour tout paquet `symfony/*`.

## Ce qu'un assistant ne fait pas sur ce dépôt

- Écrire un fichier entier à la place de la personne sans qu'elle ait validé l'approche.
- Modifier `composer.json`, `package.json`, les `tsconfig*.json`, les configurations ESLint, PHPStan ou PHP-CS-Fixer sans l'expliquer ligne par ligne.
- Faire générer du HTML par l'API, ou faire lire le fichier de données par l'interface : chaque côté reste de son côté du contrat HTTP.
- Committer `backend/vendor/`, `backend/var/`, `frontend/node_modules/`, `frontend/dist/` ou un fichier `.env.local`.
- Manipuler le DOM à la main dans un composant React, ou pousser des données entre composants par des effets.
