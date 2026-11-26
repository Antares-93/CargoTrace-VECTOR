# CargoTrace • Vector Global Shipment Visibility & Tracking Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Leaflet](https://img.shields.io/badge/Leaflet-GIS_Radar-199900?style=for-the-badge&logo=leaflet&logoColor=white)](https://leafletjs.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS_v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/PHPUnit-34_Passed_(100%25)-brightgreen?style=for-the-badge&logo=php)](https://phpunit.de)
[![Cloudflare](https://img.shields.io/badge/Cloudflare_Tunnel-Live_Online-F38020?style=for-the-badge&logo=cloudflare&logoColor=white)](https://cloudflare.com)
 
> **Brand**: Vector CargoTrace (*"Trace the Line"*)  
> **Repository**: [https://github.com/Antares-93/CargoTrace-VECTOR](https://github.com/Antares-93/CargoTrace-VECTOR)  
> **Architecture**: Clean Service-Oriented Laravel Architecture with Multi-Role RBAC, Leaflet GIS Radar, Live Weather Integration, Multi-Language (ID/EN) Switcher, User Profile Management, and RESTful Sanctum API.

---

## Executive Summary

### Problem
In international multimodal shipping and logistics, consignment data is often fragmented across ocean carriers, air cargo lines, terminal operators, and port authorities:
1. **Lack of Real-Time Corridor Visibility**: Unclear voyage milestones and missing geo-coordinates.
2. **Hidden Delays & Bottlenecks**: Maritime congestion (e.g., Suez Canal anchorage queues) and port delays are not computed deterministically.
3. **Decentralized Exception Logging**: Incident resolutions (such as customs holds, container damage, or reefer deviations) lack standardized resolution trails.

### Solution: CargoTrace
CargoTrace provides an enterprise-grade visibility and telemetry platform:
- **Interactive Live GIS Radar Map (Leaflet.js + OpenStreetMap)**: Real-time visual tracking of maritime corridors and air freight skyways with SVG vessel & plane icons, realistic sea route polylines, and port berthing countdowns.
- **Deterministic ETA & Delay Engine**: Multi-tier delay calculation (`LOW` 0–6h, `MEDIUM` 6–24h, `HIGH` >24h) tied to strict terminal status immutability.
- **Operational Incident Desk**: Complete lifecycle management for operational exceptions (`OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`) with resolution action auditing.
- **Bilingual Interface (ID / EN)**: Seamless runtime language switching between Indonesian and English across all dashboards, tables, and settings.
- **User Profile & Security Management**: Dedicated profile hub to inspect operational metrics, update contact details, and manage passwords with full security audit logging.
- **External Meteorological & Geocoding Telemetry**: Live maritime weather integration powered by Open-Meteo API and OpenStreetMap Nominatim reverse geocoding.
- **Comprehensive REST API**: Full Sanctum-authenticated JSON API contract with a ready-to-use Postman collection.

---

## Application Screenshots & Visual Tour

### 1. Landing Page (Halaman Utama Publik)
Modern public landing page showcasing global multimodal visibility, live tracking waybill search bar, features overview, and authentic partner carrier branding.
![Landing Page](docs/screenshots/01-landing-page.jpg)

---

### 2. Overview & Radar Dashboard (Menu Utama & Radar Telemetri)
Operational hub with real-time AIS/ADS-B GIS radar map, KPI counters, next port berthing countdown, and tracked cargo manifest.
![Overview & Radar Dashboard](docs/screenshots/02-overview-radar-dashboard.jpg)

---

### 3. Global Consignments Directory (Daftar & Manajemen Konsinyasi)
Comprehensive consignment manifest directory with multi-field search, transport mode filtering (Ocean, Air, Road), status filters, and schedule impact badges.
![Consignments Directory](docs/screenshots/03-consignments-directory.jpg)

---

### 4. Operational Incident Desk (Meja Insiden & Kendala)
Centralized exception management desk for port bottlenecks, weather diversions, and customs holds with resolution workflows (`OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`).
![Operational Incident Desk](docs/screenshots/04-operational-incident-desk.png)

---

### 5. Partner Carrier Registry (Operator Mitra Pelayaran & Maskapai)
Registry of authorized global shipping lines and air freight operators (Maersk, MSC, Evergreen, Qatar Cargo, Singapore Airlines, Garuda Cargo) with active/inactive status toggles.
![Partner Carrier Registry](docs/screenshots/05-partner-carrier-registry.png)

---

### 6. Global Hubs & Terminal Nodes (Pelabuhan, Bandara & Simpul Transit)
Directory of strategic logistics hubs (UN/LOCODE e.g., IDTPP, NLRTM, SGSIN, DEFRA, EGSCA) with geographical coordinates and associated consignment counters.
![Global Hubs & Terminal Nodes](docs/screenshots/06-global-hubs-nodes.png)

---

### 7. User Profile & Account Security (Profil Pengguna & Keamanan Akun)
Personal user management center displaying role-specific metrics, member status, contact information editing, and password update forms.
![User Profile & Account Security](docs/screenshots/07-user-profile-security.png)

---

### 8. System Settings Modal (Mode Tampilan & Pilihan Bahasa ID/EN)
Interactive modal allowing users to toggle between Light Mode and Nautical Dark Mode, as well as switch between English (EN) and Bahasa Indonesia (ID) instantly.
![System Settings Modal](docs/screenshots/08-system-settings-modal.png)

---

## System Architecture

CargoTrace strictly decouples HTTP transport logic from core business domain rules via a **Service Layer Architecture**:

```
[ Browser / Client / Mobile App / External Consumer ]
                          │
                          ▼
               [ Routes & Middleware ]
        (auth, role:admin,operator,customer, Sanctum)
                          │
                          ▼
                  [ Controllers ]
          (Web & Api Resource Controllers)
                          │
                          ▼
                 [ Service Layer ]
   ├── ShipmentService          ── Consignment queries, KPIs & manifests
   ├── TrackingService          ── Chronological events & status transitions
   ├── EtaService               ── Deterministic delay math & thresholds
   ├── ExceptionService         ── Incident detection & resolution notes
   ├── ExternalWeatherService   ── Real-time Open-Meteo weather telemetry
   ├── ExternalGeocodingService ── Reverse & Forward Geocoding (Nominatim)
   └── AuditService             ── Security audit trail logging
                          │
                          ▼
                 [ Eloquent Models ]
   (User, Carrier, Location, Shipment, TrackingEvent, Exception, Notification, AuditLog)
                          │
                          ▼
               [ MySQL 8.x Database ]  &  [ External APIs ]
```

---

## Entity Relationship Diagram (ERD) & Data Model

The database schema implements 9 relational models with foreign key constraints, indexes, and cascade protections:

```mermaid
erDiagram
    USERS ||--o{ SHIPMENTS : "places (customer)"
    USERS ||--o{ TRACKING_EVENTS : "logged_by"
    USERS ||--o{ SHIPMENT_EXCEPTIONS : "reported_by"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ AUDIT_LOGS : "performs"

    CARRIERS ||--o{ SHIPMENTS : "transports"
    LOCATIONS ||--o{ SHIPMENTS : "origin_of"
    LOCATIONS ||--o{ SHIPMENTS : "destination_of"
    LOCATIONS ||--o{ TRACKING_EVENTS : "at_node"

    SHIPMENTS ||--|{ SHIPMENT_ITEMS : "contains"
    SHIPMENTS ||--o{ TRACKING_EVENTS : "records"
    SHIPMENTS ||--o{ SHIPMENT_EXCEPTIONS : "encounters"
    SHIPMENTS ||--o{ NOTIFICATIONS : "triggers"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role "admin | operator | customer"
        string phone
        datetime created_at
    }

    CARRIERS {
        bigint id PK
        string code UK "MSK, EVG, QTR, MSC"
        string name
        string contact_email
        string contact_phone
        boolean is_active
    }

    LOCATIONS {
        bigint id PK
        string code UK "UN/LOCODE e.g. IDTPP, NLRTM"
        string name
        string type "SEAPORT | AIRPORT | CANAL | INLAND_HUB"
        string country
        decimal latitude
        decimal longitude
    }

    SHIPMENTS {
        bigint id PK
        string shipment_number UK
        bigint customer_id FK
        bigint carrier_id FK
        bigint origin_id FK
        bigint destination_id FK
        string status "CREATED, BOOKED, PICKED_UP, IN_TRANSIT, DELIVERED"
        string transport_mode "OCEAN | AIR | ROAD | MULTIMODAL"
        datetime expected_departure
        datetime actual_departure
        datetime expected_arrival
        datetime estimated_arrival
        datetime actual_arrival
        string delay_status "ON_TIME | LOW | MEDIUM | HIGH"
        int delay_hours
        string consignee_name
        string vessel_or_flight
        string berth_number
        int current_stage
        decimal temperature_celsius
        decimal humidity_pct
    }

    SHIPMENT_ITEMS {
        bigint id PK
        bigint shipment_id FK
        string item_name
        int quantity
        decimal weight_kg
        decimal volume_cbm
    }

    TRACKING_EVENTS {
        bigint id PK
        bigint shipment_id FK
        bigint location_id FK
        bigint created_by FK
        string status
        datetime event_time
        decimal latitude
        decimal longitude
        string description
    }

    SHIPMENT_EXCEPTIONS {
        bigint id PK
        bigint shipment_id FK
        bigint reported_by FK
        string exception_type "PORT_CONGESTION | WEATHER_DELAY | CUSTOMS_HOLD | ..."
        string severity "LOW | MEDIUM | HIGH"
        string status "OPEN | INVESTIGATING | RESOLVED"
        datetime detected_at
        datetime resolved_at
        text resolution_notes
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint shipment_id FK
        string title
        text message
        boolean is_read
        datetime created_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string entity_type
        bigint entity_id
        text old_values
        text new_values
        string ip_address
        datetime created_at
    }
```

---

## Core Business Rules

### 1. Shipment Status Lifecycle & Terminal State Immutability
Transitions follow a strict state machine:
- `CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` (or `IN_FLIGHT`, `BERTHED`) &rarr; `DELIVERED`
- **Terminal State Protection**: Once a consignment reaches `DELIVERED` or `CANCELLED`, it cannot be regressed to an earlier state without administrative override.

### 2. Deterministic ETA & Delay Calculation Rule
Whenever `estimated_arrival` is updated, the delay engine computes:
$$\Delta t = \text{estimated\_arrival} - \text{expected\_arrival}$$
- If $\Delta t \le 0$: `delay_hours = 0`, `delay_status = 'ON_TIME'`
- If $0 < \Delta t \le 6 \text{ hours}$: `delay_status = 'LOW'`
- If $6 < \Delta t \le 24 \text{ hours}$: `delay_status = 'MEDIUM'`
- If $\Delta t > 24 \text{ hours}$: `delay_status = 'HIGH'`

### 3. Role-Based Access Control (RBAC)
- **Administrator (`admin`)**: Complete system access (manage users, carriers, locations, all shipments, view audit trails, delete resources).
- **Logistics Operator (`operator`)**: Dispatch shipments, append chronological tracking events, report and resolve exceptions on the Incident Desk.
- **Customer (`customer`)**: Strictly scoped to view only their assigned consignments, tracking timelines, and relevant notifications.

---

## Live Online Deployment & Hosting

### Option 1: Instant Live Public URL via Cloudflare Tunnel (1-Click)
The repository includes an automated tunnel launcher:
1. Ensure XAMPP (MySQL) is running.
2. Run the script:
   ```bash
   python share.py
   # Or double-click: ONLINE-KAN-WEBSITE.bat
   ```
3. An active public HTTPS URL (`https://xxxx.trycloudflare.com`) is generated immediately and copied to your clipboard. Anyone can access the full live application with zero authentication barriers or anti-bot checks.

### Option 2: 24/7 Cloud Deployment (Render.com / Docker)
The application can also be deployed permanently on cloud platforms:
- Connect the GitHub repository `Antares-93/CargoTrace-VECTOR` to Render.
- Set build command: `composer install --no-dev --optimize-autoloader && npm install && npm run build`
- Set start command: `php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT`

---

## Demo Accounts

Use these pre-seeded accounts to evaluate different permission levels:

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.com` | `password` | Full system control, audit logs, carriers & hubs |
| **Logistics Operator** | `operator@cargotrace.com` | `password` | Tracking events, status transitions, incident desk |
| **Customer 1** | `customer@cargotrace.com` | `password` | PT Samudera Logistik Mitra (AT-2026-B8910, AWB-772-91024) |
| **Customer 2** | `transasia@cargotrace.com` | `password` | Trans-Asia Freight Corp (AT-2026-90412) |

> **Quick Evaluator Login**: Direct demo access routes are also available at `/demo-login/admin`, `/demo-login/operator`, and `/demo-login/customer`.

---

## REST API Specification

All API endpoints return standard JSON responses:

```json
{
    "success": true,
    "message": "Operation successful.",
    "data": { ... }
}
```

### Endpoints Overview

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/register` | Register a new user and receive Bearer Token | No |
| `POST` | `/api/login` | Authenticate and obtain Sanctum Bearer Token | No |
| `GET` | `/api/user` | Get authenticated user profile | Bearer Token |
| `POST` | `/api/logout` | Revoke active API token | Bearer Token |
| `GET` | `/api/shipments` | List consignments with filters & pagination | Bearer Token |
| `POST` | `/api/shipments` | Create consignment with cargo items | Operator / Admin |
| `GET` | `/api/shipments/{id}` | Get full shipment manifest & history | Bearer Token |
| `PUT/PATCH` | `/api/shipments/{id}` | Update shipment schedule or details | Operator / Admin |
| `DELETE` | `/api/shipments/{id}` | Delete consignment manifest | Admin only |
| `GET` | `/api/shipments/{id}/tracking` | Get chronological tracking events | Bearer Token |
| `POST` | `/api/shipments/{id}/tracking` | Record tracking event & update status | Operator / Admin |
| `GET` | `/api/carriers` | List all active shipping carriers | Bearer Token |
| `POST` | `/api/carriers` | Register a new partner carrier | Operator / Admin |
| `GET` | `/api/locations` | List global port & airport nodes | Bearer Token |
| `POST` | `/api/locations` | Add a new strategic hub node | Operator / Admin |
| `GET` | `/api/shipments/{id}/exceptions` | List exceptions on a shipment | Bearer Token |
| `POST` | `/api/shipments/{id}/exceptions` | Report an operational incident | Operator / Admin |
| `PATCH` | `/api/exceptions/{id}` | Resolve or investigate an incident | Operator / Admin |
| `GET` | `/api/weather` | Query real-time Open-Meteo maritime weather | No |
| `GET` | `/api/geocode` | Reverse/forward geocoding via Nominatim | No |

> **Postman Collection**: Import the file `CargoTrace_Postman_Collection.json` directly into Postman to test all endpoints.

---

## Installation & Local Setup

### Prerequisites
- **PHP**: 8.2 or 8.3+ with `pdo_mysql`, `curl`, `mbstring`, `openssl`
- **Composer**: 2.x
- **Node.js**: 18+ & **npm**
- **MySQL**: 8.x (or XAMPP / MariaDB)

### Setup Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Antares-93/CargoTrace-VECTOR.git
   cd CargoTrace-VECTOR
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets**:
   ```bash
   npm install
   npm run build
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure `.env` matches your MySQL setup:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cargotrace_vector
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Start Local Development Server**:
   ```bash
   php artisan serve
   ```
   Open your browser at `http://127.0.0.1:8000`.

---

## Automated Testing

CargoTrace includes an automated test suite (**34 unit & feature tests, 108 assertions**) verifying authorization, CRUD operations, lifecycle state machines, deterministic delay rules, external API integrations, user profile flows, and exception workflows.

Run tests using PHPUnit / Artisan:
```bash
php artisan test
```

### Test Coverage Highlights:
- `Tests\Feature\AuthTest`: Login validation, password failures, guest redirection, Sanctum token generation.
- `Tests\Feature\ProfileTest`: Profile viewing, name/email/phone updating, email uniqueness check, password changing with hash verification.
- `Tests\Feature\ShipmentTest`: Scoped customer queries, authorization gates, origin-destination uniqueness.
- `Tests\Feature\TrackingAndLifecycleTest`: Sequential status transitions, rejection of illegal reversions from terminal state (`DELIVERED`).
- `Tests\Feature\EtaDelayRuleTest`: Mathematical validation of `ON_TIME`, `LOW`, `MEDIUM`, and `HIGH` delay thresholds.
- `Tests\Feature\ExceptionManagementTest`: Incident reporting, delay impact propagation, resolution notes audit.
- `Tests\Feature\ExternalIntegrationTest`: Weather and geocoding API resilience with fallback telemetry.

---

## External API Integrations

1. **Open-Meteo Weather API**:
   - `GET https://api.open-meteo.com/v1/forecast`
   - Real-time temperature, humidity, wind velocity, and maritime condition codes.
   - Built with timeout handling (`timeout(3)`), error recovery, and 15-minute caching fallback.
2. **OpenStreetMap & CartoDB**:
   - High-contrast GIS nautical radar tiles rendered through Leaflet.js with SVG vessel & aircraft icons.
3. **Nominatim OpenStreetMap Geocoding**:
   - Automated latitude/longitude lookup for seaports, airports, and inland transit hubs.

---

## Attributions & Licenses
- **Framework**: [Laravel 12](https://laravel.com)
- **Map Engine**: [Leaflet.js](https://leafletjs.com) & [CartoDB](https://carto.com)
- **Weather Telemetry**: [Open-Meteo](https://open-meteo.com)
- **Icons & Styling**: [Tailwind CSS](https://tailwindcss.com) & SVG Vector Logistics Icons

---
*Built with ❤️ for Web Programming 2 • UAS Final Project.*
