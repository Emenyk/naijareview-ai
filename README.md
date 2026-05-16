# NaijaReview AI

**DSN x BCT LLM Agent Challenge 3.0** — Submission

A Laravel 13 web application implementing two LLM agent tasks:
- **Task A — User Modeling**: Simulates Yelp-style reviews in the voice of a specific user persona
- **Task B — Recommendation**: Delivers personalised, context-aware recommendations with multi-turn refinement

Built with Laravel 13, Laravel AI SDK, Mistral AI, and Tailwind CSS. Fully containerised with Docker.

---

## Quick Start (Docker — recommended for judges)

**Prerequisites:** Docker and Docker Compose installed.

```bash
# 1. Clone the repository
git clone <repo-url>
cd naijareview-ai

# 2. Copy environment file and add your Mistral API key
cp .env.example .env
# Open .env and set: MISTRAL_API_KEY=your_key_here

# 3. Build and start
docker compose up --build

# 4. Open in browser
# http://localhost:8080
```

The container automatically runs migrations on startup. No manual database setup needed.

---

## Local Development Setup

**Prerequisites:** PHP 8.3+, Composer, Node.js 20+, MySQL 8

```bash
# 1. Clone and install
git clone <repo-url>
cd naijareview-ai
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# Edit .env:
#   DB_DATABASE, DB_USERNAME, DB_PASSWORD  <- your MySQL credentials
#   MISTRAL_API_KEY=your_key_here

# 3. Run migrations
php artisan migrate

# 4. Build frontend
npm run build

# 5. Start dev server
composer run dev
# App at http://localhost:8000
```

---

## Getting a Mistral API Key

1. Go to https://console.mistral.ai
2. Create a free account and generate an API key under **API Keys**
3. Set `MISTRAL_API_KEY=your_key` in `.env`

The app also supports Anthropic, OpenAI, and Groq — change `'default'` in `config/ai.php`.

---

## Architecture

```
app/
├── Ai/Agents/
│   ├── UserModelingAgent.php        # Task A — behavioral review simulation
│   └── RecommendationAgent.php      # Task B — contextual recommendation
├── Http/Controllers/
│   ├── TaskAController.php          # Task A routes + AI invocation
│   └── TaskBController.php          # Task B routes + multi-turn session
├── Services/
│   ├── PersonaBuilder.php           # Loads and queries persona profiles
│   └── DatasetService.php           # Loads businesses, filters by domain/location/scenario
└── Helpers/
    └── NigerianContextFormatter.php # Nigerian cultural context injection

storage/app/data/
├── personas.json    # 8 detailed Yelp-derived user personas
└── businesses.json  # 60 businesses across Lagos, Abuja, Port Harcourt
```

### Agent Design

**UserModelingAgent** (Task A)
- Stateless — instantiated fresh per request with persona + product via constructor
- System prompt encodes full behavioral profile, 6 sample reviews, Nigerian context hints
- Structured output schema: `{ rating: int, review: string }`
- Rating is constrained to ±1 star from the persona's historical average

**RecommendationAgent** (Task B)
- Implements `Conversational` for multi-turn conversation history
- Constructor receives scenario type, persona description, domain, location, and filtered business catalog
- Three scenario modes handled distinctly:
  - `normal` — weighs established preferences
  - `cold_start` — popularity-weighted, preference-inferred, no history assumed
  - `cross_domain` — explicit cross-category reasoning required in output
- Conversation history stored in Laravel session, replayed as Message objects on refinement turns
- Structured output schema: `{ recommendations: [{ name, reason }] }`

### Dataset

Persona profiles and business catalog in `storage/app/data/` follow the Yelp Academic Dataset schema and were informed by analysis of the Yelp dataset (`yelp_dataset.tar`). The 8 personas cover the full spectrum of reviewer archetypes (critical, enthusiastic, analytical, brief, storytelling, elite, humorous, sentimental). The 60 businesses span restaurants, Nollywood films, Nigerian books, electronics, hotels, and wellness — all Nigeria-contextualised across Lagos, Abuja, and Port Harcourt.

---

## API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| `GET`  | `/task-a`             | User Modeling UI |
| `POST` | `/task-a/generate`    | Generate review (`persona_id`, `product`) |
| `GET`  | `/task-b`             | Recommendation UI |
| `POST` | `/task-b/recommend`   | Get recommendations (`scenario`, `persona_description`, `domain`, `location`) |
| `POST` | `/task-b/refine`      | Refine list (`refinement` prompt) |

---

## Nigerian Context

The platform is contextualised for the Nigerian market throughout:
- Persona data includes Nigerian names, Lagos/Abuja/PH locations, local food vocabulary, and Nigerian Pidgin English where authentic to each persona's voice
- Business catalog covers 60 Nigerian establishments including Terra Kulture, Nkoyo, Filmhouse, Nollywood films, Adichie and Achebe novels, Computer Village, and Abuja spas
- `NigerianContextFormatter` injects city-specific context (Lagos traffic culture, Abuja's diplomatic character, Port Harcourt's seafood identity) into recommendation prompts
- AI-generated reviews incorporate Nigerian culinary terms and local expressions where the persona's style naturally supports it

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.3, Laravel 13 |
| AI Orchestration | Laravel AI SDK 0.6 |
| AI Provider | Mistral AI (configurable to Anthropic, OpenAI, Groq) |
| Frontend | Blade templates, Tailwind CSS 4 |
| Database | MySQL 8 |
| Container | Docker, Alpine Linux, Nginx, PHP-FPM, Supervisor |

---

## License

MIT
