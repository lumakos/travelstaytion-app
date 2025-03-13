## Few words about the application

MovieWorld is a social sharing platform where users can showcase their all-time favorite movies. Users can vote
on movies, sort them by likes, dislikes, or creation date, and engage with the community.

Sign up, if you do not own an account, or login to add your own movie and join the movie list!

To enhance performance and minimize requests, Redis cache stores the movie list, sorting and voting data. When a new movie
is added, the cache is cleared, triggering fresh data retrieval from the database.Also, the same happens in case of voting.

The voting system uses synchronous events to simplify functionality and make testing easier for the Travelstaytion team.
If we need to process a large volume of data, we can implement asynchronous events and queue workers for scalability.
However, for now, I am keeping it as simple as possible. Additionally, we can use cursor pagination instead of offset
pagination to efficiently manage real-time data updates and modifications.

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

## 3. Copy envs
```
cp .env.example .env
```

## 4. Build and start the app
```
./vendor/bin/sail up -d --build
```

## 5. Run migrations and Seed database with 30movies
```
./vendor/bin/sail artisan migrate --seed
```

## 6. Key generations
```
./vendor/bin/sail artisan key:generate
```

## 7. Installing Dependencies & Starting Development Server
```
npm install && npm run dev
```

#### In case of permission issues in Storage folder, run
```
sudo chmod -R 755 storage
```

## 8. Open new tab and Re-run the app && Clear Cache
```
./vendor/bin/sail up -d --build && ./vendor/bin/sail artisan optimize:clear
```

## 9. Run Tests
```
./vendor/bin/sail artisan test
```

#### Running host
```
http://localhost:100
```
## Commands

### Database Seeder
```
./vendor/bin/sail db:seed
```

### Clear Laravel Cache
```
./vendor/bin/sail artisan optimize:clear
```

### Flush Redis
```
./vendor/bin/sail redis

FLUSHDB
```

### Thank you for your time and the opportunity to complete this assignment. I am available for any questions you may have.
