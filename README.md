# 📋 TaskBoard

Una piccola applicazione per la gestione di progetti e task, costruita come progetto di apprendimento per consolidare pattern e best practice dell'ecosistema Laravel.

**🔗 Demo live:** [taskboard-61n5.onrender.com](https://taskboard-61n5.onrender.com/)
*(hosting gratuito: la prima richiesta dopo un periodo di inattività può richiedere qualche secondo per "risvegliare" il servizio)*

Credenziali di prova: `test@example.com` / `password`

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)
![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=flat&logo=vuedotjs&logoColor=white)
![Inertia](https://img.shields.io/badge/Inertia.js-v3-9553E9?style=flat)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![Pest](https://img.shields.io/badge/Pest-tested-6ba5f7?style=flat)
![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php&logoColor=white)

---

## ✨ Funzionalità

- **Progetti**: creazione, modifica, eliminazione, elenco per utente
- **Task**: creazione, modifica, eliminazione, completamento, filtro per stato e priorità
- **Autenticazione** semplice (login/logout)
- **Autorizzazione** granulare: ogni utente vede e gestisce solo i propri progetti/task
- **Regole di business**:
  - un progetto può avere al massimo **20 task non completate**
  - una task **completata non può tornare** a "in progress"
- Interfaccia web (Inertia + Vue) **e** API JSON, esposte come due layer separati sopra la stessa business logic

## 🏗️ Architettura

Il progetto segue un'architettura a layer con responsabilità ben separate:

```
Request → Form Request (validazione) → Controller → Policy (autorizzazione)
        → Service (business logic) → Repository (accesso ai dati) → Model → Database
```

- **Controllers** sottili: autorizzano, delegano al Service, restituiscono la risposta
- **Services** contengono la business logic (es. limite di 20 task, transizione di stato vietata)
- **Repository + Interface** disaccoppiano l'accesso ai dati dal resto dell'applicazione, con binding nel `AppServiceProvider`
- **Policies** gestiscono l'autorizzazione (proprietà del progetto/task)
- Sia il **controller JSON API** (`/projects/...`) sia il **controller delle pagine Inertia** (`/app/projects/...`) riusano **gli stessi Service, Policy e Form Request** — la business logic vive in un solo posto, indipendentemente dal canale che la espone

```
app/
├── Http/
│   ├── Controllers/      # JSON API + pagine Inertia (separati, stessa business logic)
│   ├── Requests/         # validazione input
├── Interfaces/           # contratti dei repository
├── Repositories/         # accesso ai dati
├── Services/             # business logic
├── Policies/              # autorizzazione
├── Models/                # Eloquent
└── Enums/                 # Status, Priority
```

## 🧱 Stack tecnico

| Layer      | Tecnologia                          |
|------------|--------------------------------------|
| Backend    | Laravel 13, PHP 8.5                  |
| Frontend   | Vue 3 + Inertia.js v3                |
| Styling    | Tailwind CSS 4                       |
| Routing FE | Laravel Wayfinder (route/action tipizzate in TS) |
| Test       | Pest                                 |
| Hosting    | Render (Docker) + PostgreSQL         |

## 🚀 Setup locale

```bash
# clona il repo
git clone https://github.com/noticso/TaskBoard.git
cd TaskBoard

# backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# frontend
npm install
npm run build   # oppure `npm run dev` durante lo sviluppo

# avvio
composer run dev   # server Laravel + Vite in un solo comando
```

L'app sarà disponibile su `http://localhost:8000`.

## 🧪 Test

```bash
php artisan test --compact
# oppure, per un singolo file:
php artisan test tests/Feature/Http/Controllers/TaskControllerTest.php
```

## 📦 Deploy

L'app è containerizzata (`Dockerfile`) e pubblicata su [Render](https://render.com) tramite Blueprint (`render.yaml`), con database PostgreSQL gestito. Il comando di avvio esegue cache di configurazione/rotte, migration e seeding a ogni deploy.
