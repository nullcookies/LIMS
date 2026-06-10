# LIMS — Architecture Document

## 1. System Overview

**LIMS** (Laboratory Information Management System) — система управления лабораторными исследованиями.
Стек: **Symfony 6/7** (backend) + **React 18+** (frontend) + **PostgreSQL** (БД).

### Core Domains
| Domain | Описание |
|--------|----------|
| Sample Management | Регистрация, трекинг, хранение образцов |
| Test Catalog | Справочник анализов, методик, нормативов |
| Workflow Engine | Маршруты образцов, статусы, валидация |
| Results | Ввод, расчёт, утверждение результатов |
| Quality Control | Внутрилабораторный контроль, профпригодность |
| Customer/CRM | Контрагенты, договоры, заявки |
| Instrument Integration | API-шлюз для приборов |
| Billing | Тарификация, счета, акты |
| Inventory | Реагенты, расходники, оборудование |
| Compliance | ISO 17025, GDPR, 152-ФЗ, метрология |

---

## 2. Backend Architecture (Symfony)

### Layers (DDD)

```
┌─────────────────────────────────────────┐
│              Presentation               │
│   REST API (API Platform) / GraphQL     │
├─────────────────────────────────────────┤
│              Application                │
│   Commands / Queries (CQRS) / DTO      │
├─────────────────────────────────────────┤
│                Domain                   │
│   Entities / Value Objects / Events     │
│   Domain Services / Specifications      │
├─────────────────────────────────────────┤
│              Infrastructure              │
│   Doctrine / Messenger / Cache / Queue  │
│   External APIs (B24, Instruments)      │
└─────────────────────────────────────────┘
```

### Key Symfony Bundles
- `api-platform/core` — REST/GraphQL API
- `symfony/messenger` — async commands & events
- `doctrine/doctrine-bundle` — ORM + migrations
- `lexik/jwt-authentication-bundle` — JWT auth
- `friendsofsymfony/elastica-bundle` — full-text search (Elasticsearch)
- `sonata-project/admin-bundle` — admin panel (optional)
- `vich/uploader-bundle` — file uploads
- `nelmio/cors-bundle` — CORS
- `gesdinet/jwt-refresh-token-bundle` — refresh tokens
- `hautelook/AliceBundle` — fixtures

### CQRS / Event Sourcing
- **Commands**: `RegisterSampleCommand`, `SubmitResultCommand`
- **Queries**: `GetSampleQuery`, `ListTestsQuery`
- **Events**: `SampleRegistered`, `ResultApproved`, `QcFailed`
- Bus: Symfony Messenger (sync + async transports)

### Authentication & Authorization
- JWT (LexikJWTAuthenticationBundle) + Refresh Tokens
- RBAC — роли: ROLE_TECHNICIAN, ROLE_ANALYST, ROLE_SUPERVISOR, ROLE_ADMIN
- Voters для object-level permissions
- API-ключи для M2M (инструменты, внешние системы)

### Async Processing (Messenger)
| Transport | Назначение |
|-----------|------------|
| `async_low` | Email, SMS, уведомления |
| `async_high` | Расчёт результатов, генерация отчётов |
| `instrument` | Очередь данных с приборов |
| `sync` | Команды, требующие немедленного ответа |

---

## 3. Frontend Architecture (React)

### Tech Stack
- **React 18** + TypeScript
- **Vite** — сборка
- **React Router v6** — routing
- **TanStack Query (React Query)** — server state
- **Zustand** — client state (UI, filters)
- **MUI 6** — компоненты
- **React Hook Form + Zod** — формы
- **Recharts + D3** — графики
- **Socket.IO / SSE** — real-time

### Module Structure (Feature-Sliced)
```
src/
├── features/
│   ├── samples/          # Регистрация, трекинг образцов
│   ├── tests/            # Каталог анализов
│   ├── results/          # Ввод и утверждение результатов
│   ├── qc/               # Контроль качества
│   ├── customers/        # Контрагенты
│   ├── instruments/      # Интеграция с приборами
│   ├── billing/          # Биллинг
│   ├── inventory/        # Склад
│   └── reports/          # Отчёты
├── shared/               # UI-kit, хуки, utils
├── widgets/              # Композитные блоки (дашборды)
└── pages/                # Роуты
```

### Key Pages
- `/dashboard` — дашборд (статусы, метрики, графики)
- `/samples` — реестр образцов (DataGrid + фильтры)
- `/samples/:id` — карточка образца (канбан статусов)
- `/tests` — справочник анализов
- `/results` — ввод результатов (batch mode)
- `/results/approval` — очередь на утверждение
- `/qc` — контрольные карты Шухарта, Levey-Jennings
- `/customers` — CRM-секция
- `/instruments` — приборы + интеграция
- `/reports` — генератор отчётов
- `/settings` — системные настройки

---

## 4. Database Design

### Core Tables

```sql
-- Пользователи / Роли
users (id, email, roles, password, name, is_active)
roles (id, name, permissions)

-- Контрагенты (CRM)
customers (id, name, inn, kpp, contacts, address, b24_id)
customer_contacts (id, customer_id, name, phone, email)

-- Образцы
sample_types (id, name, description)
samples (id, uuid, barcode, customer_id, sample_type_id,
         status, created_at, updated_at, metadata_json)
sample_status_history (id, sample_id, from_status, to_status,
                       user_id, created_at)

-- Каталог анализов
test_methods (id, code, name, unit, price, norm_min, norm_max)
test_profiles (id, name, tests_json) -- наборы анализов

-- Заказы (связь образцов с анализами)
sample_tests (id, sample_id, test_method_id, status,
              result_value, result_text, approved_by, approved_at,
              uncertainty, qc_status)

-- Инструменты
instruments (id, name, model, serial_number, last_calibration,
             api_endpoint, protocol)
instrument_readings (id, instrument_id, sample_test_id,
                     raw_data_json, read_at)

-- Контроль качества
qc_samples (id, test_method_id, control_type, expected_value,
            measured_value, date, approved_by, status)
qc_rules (id, test_method_id, rule_type, params_json)

-- Инвентарь
reagents (id, name, lot, quantity, unit, expires_at, supplier_id)
inventory_transactions (id, reagent_id, type, quantity, user_id)

-- Биллинг
price_lists (id, test_method_id, price, valid_from, valid_to)
invoices (id, customer_id, amount, status, paid_at, items_json)

-- Аудит
audit_log (id, user_id, entity_type, entity_id, action,
           old_value_json, new_value_json, ip, created_at)
```

### Patterns
- UUID как первичные ключи (для распределённой генерации)
- JSONB для метаданных (гибкая схема)
- `created_at` / `updated_at` во всех сущностях
- Soft-delete через `deleted_at`
- Enum-статусы в отдельных таблицах (не строки)
- Индексы на `(customer_id, status)`, `(barcode)`, `(created_at)`

---

## 5. Integration Layer

### Bitrix24 Integration
- **REST API** (битрикс24-connector bundle)
- **Webhooks** для real-time событий (сделки, лиды)
- **Синхронизация**: Customers ↔ B24 Contacts/Companies
- **Deals**: заказ = сделка в B24 с товарными позициями
- **Tasks**: утверждение результата → задача исполнителю
- **OAuth 2.0** авторизация

### Instrument Integration
- **API Gateway** (Symfony) — единая точка входа
- **Протоколы**: ASTM, HL7, CSV, REST, Modbus
- **Middleware**: нормализация данных в единый формат
- **Queue**: данные с прибора → Messenger transport → обработка
- **Retry + DLQ** для сбоев

### Email/SMS
- Symfony Mailer (SMTP или SES)
- SMS: Twilio / smsc.ru (через Messenger)

### Document Generation
- Twig → PDF (knp-snappy / wkhtmltopdf)
- Экспорт: XLSX (PhpSpreadsheet), CSV, XML

---

## 6. AI/ML & Agents

### Use Cases
| Задача | Подход |
|--------|--------|
| Аномалии в QC | Isolation Forest на исторических данных |
| Предсказание срока годности реагентов | Time-series forecasting (Prophet) |
| NLP-запросы к БД | LLM → text-to-SQL |
| Auto-валидация результатов | Классификатор (outlier detection) |
| Оптимизация загрузки приборов | Scheduling agent (OR-tools) |
| Генерация протоколов | LLM (шаблон + данные) |

### Architecture for AI Agents
- **AI Gateway** — отдельный микросервис (Python FastAPI)
- Communication: RabbitMQ / Redis PubSub
- Agents: LangChain / CrewAI / AutoGen
- Vector Store: PostgreSQL (pgvector) для RAG
- Результаты → Events → Symfony Messenger

---

## 7. Infrastructure & DevOps

```
                        ┌──────────┐
                        │  Nginx   │
                        └────┬─────┘
                     ┌───────┼───────┐
                     │       │       │
                ┌────┴──┐ ┌──┴──┐ ┌──┴────┐
                │ Symfony│ │ Vite│ │ AI GW │
                └───┬───┘ └─────┘ └───┬───┘
                    │                  │
              ┌─────┴─────┐     ┌─────┴─────┐
              │ PostgreSQL │     │  Python   │
              │   Redis    │     │  FastAPI  │
              │  Elastic   │     │  pgvector │
              │  RabbitMQ  │     └───────────┘
              └────────────┘
```

### Containerization (Docker)
- `docker-compose.yml` — dev-окружение
- Multi-stage Dockerfiles для production
- PHP-FPM + Nginx + Node/Vite

### CI/CD (GitLab / GitHub Actions)
- Lint: PHPStan lvl 9, ESLint, Prettier
- Tests: PHPUnit, Behat, Vitest, Playwright
- Security: `symfony security-checker`, Snyk
- Deploy: blue/green на Kubernetes

### Monitoring
- Sentry — exceptions
- Prometheus + Grafana — метрики (requests, queue, DB)
- ELK — логи
- Health checks (`/health`, `/ready`)

---

## 8. Quality Assurance

### Testing Pyramid
```
         ╱── E2E (Playwright) ──╲
       ╱── Integration (Behat) ──╲
     ╱── Component (Storybook) ───╲
   ╱── Unit (PHPUnit + Vitest) ────╲
```

- **Unit**: 80%+ coverage на Domain layer
- **Integration**: Repository, API endpoints (test DB)
- **E2E**: Критические flows (регистрация → результат)
- **Mutation testing**: Infection PHP

### Code Quality Tools
- PHPStan (level max) + Psalm
- PHP-CS-Fixer / Easy Coding Standard
- Rector — automated refactoring
- ESLint + Prettier (frontend)
- Husky + lint-staged (pre-commit hooks)

### Performance
- Doctrine second-level cache (Redis)
- Elasticsearch для поиска (против LIKE по таблицам)
- Pagination (cursor-based для больших таблиц)
- N+1 детекция (Doctrine `profile`)
- Vite code splitting (lazy routes)

---

## 9. API Design (API Platform)

### Standards
- RESTful + GraphQL
- OpenAPI 3.1 (авто-документация)
- JSON:API или HAL
- Versioning: через `Accept` header / URL prefix `/v1/`
- Rate limiting: 1000 req/min (Symfony rate limiter)

### Endpoints (examples)
```
GET    /v1/samples                    — список (пагинация, фильтры)
POST   /v1/samples                    — создать образец
GET    /v1/samples/{id}               — детали
PATCH  /v1/samples/{id}               — обновить
POST   /v1/samples/{id}/submit        — отправить на анализ
POST   /v1/results/batch              — массовый ввод результатов
POST   /v1/results/{id}/approve       — утвердить
GET    /v1/customers/{id}/deals       — сделки B24
POST   /v1/instruments/{id}/reading   — данные с прибора
```
