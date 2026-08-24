# pers_book

Squelette de monorepo pour une bibliotheque personnelle : une API Symfony et un front Vue 3,
tous deux initialises mais sans code metier.

## Etat du depot

C'est un point de depart, pas une application fonctionnelle.

- `grozef_back_book/` : projet Symfony genere (`src/Kernel.php`, `config/`, `migrations/`).
  `src/Entity/`, `src/Controller/` et `src/Repository/` sont vides.
- `grozef_front_book/` : projet Vue 3 genere. Vues `HomeView` et `AboutView`, composants
  `HelloWorld`, `TheWelcome`, `WelcomeItem` et le store `counter` — tout provient du modele
  de depart.

## Stack prevue

**Back** : Symfony, Doctrine ORM avec migrations, Twig, Messenger sur transport Doctrine.
**Front** : Vue 3, Vue Router, Pinia, Vite, avec Vitest et Cypress deja configures.

## Lancer

```sh
cd grozef_back_book && composer install
cd ../grozef_front_book && npm install && npm run dev
```

`compose.yaml` fournit les services de developpement cote back.
