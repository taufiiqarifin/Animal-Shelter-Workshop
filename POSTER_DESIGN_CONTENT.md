# ANIMAL RESCUE & ADOPTION MANAGEMENT SYSTEM
## Academic Poster Design Content

---

## HEADER SECTION

### Title
**ANIMAL RESCUE & ADOPTION MANAGEMENT SYSTEM**

### Logos
- Malaysian Government (Kementerian Pendidikan Malaysia)
- UTeM Logo
- FTMK Logo
- Industry Innovation Icons (if applicable)

### Course Information
**BITU3923 - WORKSHOP II**

### Supervised By
**TS. DR. NORASHIKIN BINTI AHMAD**

### Prepared By
- **TAUFIQ** (Matric Number) - User Management Module
- **EILYA** (Matric Number) - Stray Reporting Module
- **SHAFIQAH** (Matric Number) - Animal Management Module
- **ATIQAH** (Matric Number) - Shelter Management Module
- **DANISH** (Matric Number) - Booking & Adoption Module

---

## INTRODUCTION (Green rounded box)

The Animal Rescue & Adoption Management System is a comprehensive web-based platform built with **Laravel 11** to streamline animal rescue operations, medical record management, shelter inventory tracking, and adoption processes.

The system implements a **distributed database architecture** across **5 separate databases** running on different engines (MySQL, PostgreSQL, SQL Server) to demonstrate enterprise-level database management and cross-database integration.

It provides an efficient, user-friendly way for the public to report stray animals, for caretakers to manage rescue operations, and for adopters to find and adopt their perfect companion.

---

## PROBLEMS (Orange rounded box)

1. **Manual Record-Keeping**: Traditional animal shelters rely on paper-based or basic spreadsheet systems, leading to data loss, duplication, and inefficiency.

2. **Limited Public Engagement**: No centralized platform for the public to report stray animals or browse adoptable animals online.

3. **Fragmented Data Management**: Medical records, shelter inventory, bookings, and adoption records are often stored separately, making it difficult to get a complete view.

4. **No Real-Time Tracking**: Difficulty tracking animal locations, rescue operations, and adoption appointment schedules in real-time.

5. **Payment Integration Challenges**: Manual payment processing for adoption fees and donations without proper transaction tracking.

---

## OBJECTIVES (Orange rounded box)

1. To develop a centralized platform for managing animal rescue operations with **geolocation-based reporting** and rescue tracking.

2. To establish a **distributed database management system** that demonstrates cross-database relationships and ensures data integrity across multiple database engines.

3. To provide comprehensive medical record tracking including vaccinations, clinic visits, and veterinary care for rescued animals.

4. To implement an efficient **booking and adoption system** with integrated payment processing (ToyyibPay) and appointment scheduling.

5. To enable **role-based access control** (Admin, Caretaker, User) for secure and organized operations.

6. To generate analytical dashboards with **metrics and visualizations** for monitoring rescue operations, shelter capacity, and adoption rates.

---

## PROCESS FLOW

### User Journey Flow

```
┌──────────────┐
│ Public User  │
└──────┬───────┘
       │
       ├──→ Browse Animals → View Details → Add to Visit List → Book Appointment
       │
       └──→ Report Stray Animal → Upload Location & Photos → Submit Report
                                                                      ↓
┌──────────────┐                                          ┌────────────────┐
│  Caretaker   │                                          │ Rescue Created │
└──────┬───────┘                                          └────────┬───────┘
       │                                                            │
       ├──→ View Reports → Assign Self → Conduct Rescue → Register Animal
       │                                                            ↓
       ├──→ Manage Animal Records → Medical History → Vaccinations
       │                                                            ↓
       └──→ Assign Shelter Slot → Update Inventory       ┌────────────────┐
                                                          │ Animal Ready   │
┌──────────────┐                                          │ for Adoption   │
│    Admin     │                                          └────────┬───────┘
└──────┬───────┘                                                   │
       │                                                            ↓
       ├──→ User Management → Assign Roles                 ┌────────────────┐
       │                                                    │ User Books     │
       ├──→ System Configuration → Database Monitoring     │ Appointment    │
       │                                                    └────────┬───────┘
       └──→ Analytics Dashboard → Generate Reports                  │
                                                                     ↓
                                                          ┌────────────────┐
                                                          │ Payment via    │
                                                          │ ToyyibPay      │
                                                          └────────┬───────┘
                                                                   │
                                                                   ↓
                                                          ┌────────────────┐
                                                          │ Adoption       │
                                                          │ Completed      │
                                                          └────────────────┘
```

---

## DATABASE ARCHITECTURE (Key Feature - Highlight in green box)

### Distributed Database System (5 Databases)

```
┌─────────────────────────────────────────────────────────────────┐
│                    DISTRIBUTED ARCHITECTURE                      │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   TAUFIQ     │      │    EILYA     │      │  SHAFIQAH    │
│ PostgreSQL   │      │   MySQL      │      │   MySQL      │
│  Port 5434   │      │  Port 3307   │      │  Port 3309   │
├──────────────┤      ├──────────────┤      ├──────────────┤
│ • Users      │      │ • Reports    │      │ • Animals    │
│ • Roles      │      │ • Rescues    │      │ • Medical    │
│ • Adopter    │      │ • Images     │      │ • Vaccination│
│   Profiles   │      │              │      │ • Clinics    │
│              │      │              │      │ • Vets       │
│              │      │              │      │ • Animal     │
│              │      │              │      │   Profiles   │
└──────────────┘      └──────────────┘      └──────────────┘

┌──────────────┐      ┌──────────────┐
│   ATIQAH     │      │   DANISH     │
│   MySQL      │      │ SQL Server   │
│  Port 3308   │      │  Port 1434   │
├──────────────┤      ├──────────────┤
│ • Slots      │      │ • Bookings   │
│ • Sections   │      │ • Adoptions  │
│ • Inventory  │      │ • Transaction│
│ • Categories │      │ • VisitLists │
└──────────────┘      └──────────────┘

┌─────────────────────────────────────────────────────────────────┐
│           Cross-Database Relationships (Application Layer)       │
│                                                                  │
│  Animal (shafiqah) ←→ Rescue (eilya)                           │
│  Animal (shafiqah) ←→ Slot (atiqah)                            │
│  Animal (shafiqah) ←→ Booking (danish)                         │
│  Animal (shafiqah) ←→ Image (eilya)                            │
│  User (taufiq) ←→ Booking (danish)                             │
│  User (taufiq) ←→ Report (eilya)                               │
└─────────────────────────────────────────────────────────────────┘
```

### Key Technical Features:
- **3 Database Engines**: MySQL, PostgreSQL, SQL Server
- **Cross-Database Relationships**: Managed at application layer
- **Foreign Key Validation**: Enforced programmatically (not at DB level)
- **Distributed Transactions**: Coordinated across multiple connections
- **Custom Artisan Command**: `db:fresh-all` for multi-database migrations

---

## TECHNOLOGY STACK

### Backend Framework
- **Laravel 11** (PHP 8.2+)
- Livewire 3.6 for dynamic components
- Spatie Laravel Permission (RBAC)
- Laravel Breeze (Authentication)

### Frontend Technologies
- Blade Templates
- Tailwind CSS
- Alpine.js
- Responsive Design

### Database Systems
- MySQL (3 databases)
- PostgreSQL (1 database)
- SQL Server (1 database)

### Testing
- Pest PHP Testing Framework
- SQLite in-memory for testing

### Third-Party Integration
- **ToyyibPay** Payment Gateway
- **Google Maps API** for geolocation
- **Leaflet.js** for interactive maps

---

## KEY FEATURES

### 1. Stray Reporting Management (Eilya Module)
- Public can report stray animals with geolocation
- Photo upload with multiple images
- Interactive rescue map with clustering
- Caretaker assignment and rescue tracking

### 2. Animal Management (Shafiqah Module)
- Comprehensive animal profiles (breed, age, gender, color)
- Medical history tracking
- Vaccination schedules
- Clinic and veterinarian management
- Animal matching algorithm for adopters

### 3. Shelter Management (Atiqah Module)
- Shelter slot allocation system
- Section-based organization
- Inventory tracking (food, medicine, supplies)
- Category management
- Real-time capacity monitoring

### 4. Booking & Adoption System (Danish Module)
- Visit list feature (save animals to visit)
- Appointment booking system
- **Multi-layer booking prevention** (no double bookings)
- ToyyibPay payment integration
- Transaction tracking
- Adoption record management

### 5. User Management (Taufiq Module)
- Role-based access control (Admin, Caretaker, User)
- User profiles with adopter matching
- Permission management
- Activity tracking

### 6. Analytics Dashboard (Livewire)
- Real-time metrics and visualizations
- Rescue operation statistics
- Adoption rate tracking
- Shelter capacity overview

---

## SECURITY & DATA INTEGRITY

### Multi-Layer Defense System
- **Layer 1**: Add to visit list validation (prevents booking conflicts)
- **Layer 2**: Booking confirmation validation (cross-checks all active bookings)
- **Layer 3**: Time slot conflict check (redundant safety)

### Database Connection Monitoring
- Smart caching system (30 min when healthy, 60 sec when offline)
- Real-time connection status modal
- Automatic recovery detection
- Session-based status persistence

### Role-Based Access Control
- Admin: Full system access, user management
- Caretaker: Rescue operations, animal management
- User: Browse animals, book appointments, report strays

---

## INTERFACE SECTIONS

### Admin Interface
- **User Management Dashboard**: Assign roles, manage permissions
- **System Monitoring**: Database connection status, health checks
- **Analytics**: Comprehensive reports and visualizations

### Caretaker Interface
- **Rescue Map**: Interactive map with reported strays
- **Animal Management**: CRUD operations, medical records
- **Shelter Management**: Slot assignment, inventory tracking

### Public User Interface
- **Browse Animals**: Filter by species, breed, gender, availability
- **Animal Details**: Photos, medical history, personality traits
- **Visit List**: Save favorite animals
- **Booking System**: Schedule appointments, payment processing
- **Report Stray**: Geolocation-based reporting with photos

---

## DEVELOPMENT COMMANDS

### Setup & Migration
```bash
composer setup          # Initial setup
php artisan db:fresh-all --seed  # Multi-database migration
```

### Development Server
```bash
composer dev           # Concurrent: server + queue + logs + vite
```

### Database Operations
```bash
php artisan db:clear-status-cache  # Clear connection status cache
```

---

## ENTITY RELATIONSHIP DIAGRAM

### Core Entities (Simplified View)

**User Management (Taufiq - PostgreSQL)**
- users (id, name, email, password, role)
- roles (id, name)
- adopter_profiles (id, user_id, preferences)

**Stray Reporting (Eilya - MySQL)**
- reports (id, user_id, latitude, longitude, description)
- rescues (id, report_id, caretaker_id, rescue_date)
- images (id, animal_id, report_id, image_path)

**Animal Management (Shafiqah - MySQL)**
- animals (id, rescue_id, slot_id, name, species, breed, status)
- medical (id, animal_id, vet_id, clinic_id, diagnosis, treatment)
- vaccinations (id, animal_id, vaccine_type, vaccination_date)
- clinics (id, name, address, phone)
- vets (id, name, license_number)
- animal_profiles (id, animal_id, temperament, energy_level)

**Shelter Management (Atiqah - MySQL)**
- slots (id, section_id, slot_number, capacity, status)
- sections (id, name, description)
- inventory (id, category_id, item_name, quantity, status)
- categories (id, name, type)

**Booking & Adoption (Danish - SQL Server)**
- bookings (id, user_id, appointment_date, appointment_time, status)
- animal_booking (booking_id, animal_id) [pivot table]
- visit_lists (id, user_id, animal_id)
- adoptions (id, booking_id, adoption_date, adoption_fee)
- transactions (id, adoption_id, amount, payment_method, toyyibpay_bill_code)

---

## TESTING & QUALITY ASSURANCE

### Testing Strategy
- **Unit Tests**: Model relationships, business logic
- **Feature Tests**: Controller actions, form submissions
- **Integration Tests**: Cross-database operations
- **Browser Tests**: End-to-end user flows

### Code Quality
- Laravel Pint (PHP CS Fixer)
- PSR-12 Coding Standards
- Automated code formatting

### Database Testing
- SQLite in-memory for fast test execution
- Test seeders for consistent data
- Transaction rollback after each test

---

## PROJECT ACHIEVEMENTS

✅ Successfully implemented distributed database architecture across 5 databases
✅ Cross-database relationship management at application layer
✅ Multi-engine support (MySQL, PostgreSQL, SQL Server)
✅ Custom Artisan commands for distributed operations
✅ Real-time geolocation mapping with clustering
✅ Payment gateway integration (ToyyibPay)
✅ Role-based access control with Spatie Permission
✅ Comprehensive booking prevention system
✅ Smart database connection monitoring
✅ Livewire dashboard with real-time metrics
✅ Responsive design with Tailwind CSS

---

## FUTURE ENHANCEMENTS

1. **Mobile Application**: React Native app for iOS/Android
2. **Email Notifications**: Automated alerts for bookings, adoptions, rescues
3. **SMS Integration**: Appointment reminders via SMS
4. **AI-Powered Matching**: Machine learning for adopter-animal matching
5. **Multi-Language Support**: Bahasa Malaysia + English
6. **Volunteer Management**: Register and assign volunteers to rescue operations
7. **Donation Platform**: Crowdfunding for animal medical expenses
8. **API Development**: RESTful API for third-party integrations

---

## CONCLUSION

The Animal Rescue & Adoption Management System demonstrates advanced full-stack development skills with distributed database architecture, cross-database relationship management, payment integration, and real-time geolocation features.

The project showcases enterprise-level database design patterns, Laravel best practices, and modern web development technologies while addressing a real-world social need for efficient animal rescue and adoption operations.

---

## REFERENCES

- Laravel 11 Documentation: https://laravel.com/docs/11.x
- Spatie Laravel Permission: https://spatie.be/docs/laravel-permission
- ToyyibPay API Documentation: https://toyyibpay.com/apireference
- Leaflet.js Documentation: https://leafletjs.com
- Livewire 3 Documentation: https://livewire.laravel.com

---

## DESIGN NOTES FOR POSTER CREATION

### Color Scheme (Match Reference Style)
- **Primary Green**: #2D5016 (for headers, main boxes)
- **Light Green**: #E8F5E9 (for backgrounds)
- **Orange/Yellow**: #F57C00 (for Problems/Objectives boxes)
- **White**: #FFFFFF (for text backgrounds)
- **Dark Text**: #1B5E20 (for body text)

### Layout Structure (Portrait A1 Size)
1. **Top 15%**: Header (logos, title, team members)
2. **Next 20%**: Introduction + Problems + Objectives (3 rounded boxes)
3. **Middle 35%**: Process Flow + Database Architecture (2 large sections)
4. **Bottom 30%**: ERD + Interface Screenshots (2 large sections)

### Visual Elements to Include
- Car/animal icons for process flow
- Database cylinder icons for architecture diagram
- Screenshots of actual interfaces
- Flowchart arrows and connectors
- ERD with proper entity boxes and relationship lines

### Typography
- **Title**: Bold, 72pt
- **Section Headers**: Bold, 48pt
- **Body Text**: Regular, 24-28pt
- **Captions**: Regular, 18-20pt

### Tools Recommended
- Canva (easiest for beginners)
- PowerPoint (familiar interface)
- Adobe Illustrator (professional)
- Figma (collaborative design)
