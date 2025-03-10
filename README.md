# Few words about the application
```
A small project named MovieWorld. The application is a social sharing platform where users can share their 
favorite movies of all time. Also, they can vote and sort the movies by likes, hates and created date.
```
---

## Features

- **User Authentication** with **Laravel Breeze**.
- **Movie List**: Actions for **creating, voting, sorting leads**.
- **Data Validation** and **secure password storage**.
- **Logging & Error Handling** to capture failed operations.

---

## Technologies Used

- **Laravel 10** (Backend Framework)
- **MySQL** (SQL Database)
- **Redis** (Caching)
- **Laravel Breeze** (Token-based Authentication)
- **Laravel Queues & Horizon** (Job Processing)

---

## Installation & Setup

## 1. Clone the Repository
```
git clone git@github.com:lumakos/travelstaytion-app.git
cd travelstaytion-app
```

## 2. Install Dependencies
```
composer install
```

## 3. Build and start the app
```
./vendor/bin/sail up -d --build
```

## 4. Copy envs
```
cp .env.example .env
```

## 5. Run migrtions
```
./vendor/bin/sail artisan migrate --seed
```

## 6. Key generations
```
./vendor/bin/sail artisan key:generate
```
#### In case of permission issues, run
```
sudo chmod -R 755 travelstaytion-app
```

## 7. Re-run the app
```
./vendor/bin/sail up -d --build
```

## 8. Run Tests
```
./vendor/bin/sail artisan test
```

#### Running host
```
http://localhost:100
```

### Database Seeder
```
./vendor/bin/sail db:seed
```

### Flush Redis
```
./vendor/bin/sail redis

FLUSHDB
```
