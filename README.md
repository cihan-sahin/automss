# Message Sending System

A Laravel 11-based automatic message sending system designed for bulk messaging operations. This project implements a queue-based architecture with rate limiting to send messages efficiently and reliably.

## ✨ Features

- **Automatic Message Sending**: Queue-based message delivery system
- **Rate Limiting**: Sends 2 messages every 5 seconds
- **Repository Pattern**: Clean code architecture with repository pattern implementation
- **Service Layer**: Business logic separation
- **Queue Jobs**: Laravel queue system for async message processing
- **Redis Caching**: Message IDs and sending times cached in Redis
- **RESTful API**: API endpoint to retrieve sent messages
- **Swagger Documentation**: Complete API documentation with Swagger/OpenAPI
- **Unit & Integration Tests**: Comprehensive test coverage
- **Docker Support**: Containerized development environment

## 📦 Requirements

- PHP 8.3 or higher
- Composer
- MySQL
- Redis Server

## 🚀 Installation

### Using Docker (Recommended - with MySQL)

1. **Start all services**
   ```bash
   docker-compose up -d
   ```

   This will start:
   - MySQL 8.0 (database)
   - Redis (cache)
   - Laravel App (web server on port 8000)
   - Queue Worker (background jobs)

2. **Seed the database** (optional)
   ```bash
   docker-compose exec app php artisan db:seed
   ```

3. **Access the application**
   - API: http://localhost:8000/api/messages
   - Swagger: http://localhost:8000/api/documentation

4. **Process messages**
   ```bash
   docker-compose exec app php artisan messages:process
   ```

5. **View logs**
   ```bash
   docker-compose logs -f app
   docker-compose logs -f queue
   ```

6. **Stop services**
   ```bash
   docker-compose down
   ``

## ⚙️ Configuration

### Environment Variables

Update your `.env` file with the following configurations:

#### For Local Development (without Docker)
```env
# Database (SQLite)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=message_system
DB_USERNAME=laravel
DB_PASSWORD=secret

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=database

# Redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Webhook Configuration
WEBHOOK_URL=https://webhook.site/9afef4d6-b069-48b6-8b29-4e53536d9573
``` 
### Adding Test Messages

You can add messages via the database seeder or manually:

```bash
# Using seeder
php artisan db:seed --class=MessageSeeder

# Using tinker
php artisan tinker
>>> App\Models\Message::create([
    'phone_number' => '+905551111111',
    'content' => 'Test message',
]);
```

## 📚 API Documentation

### Endpoints

#### GET /api/messages
Returns a list of all sent messages.

**Response Example:**
```json
{
  "success": true,
  "count": 5,
  "data": [
    {
      "id": 1,
      "phone_number": "+905551111111",
      "content": "Insider - Project",
      "message_id": "67f2f8a8-ea58-4ed0-a6f9-ff217df4d849",
      "sent_at": "2025-12-30T19:15:00+00:00",
      "created_at": "2025-12-30T19:10:00+00:00"
    }
  ]
}
```

### Swagger UI

Access the interactive API documentation:

```
http://localhost:8000/api/documentation
```

Generate/Regenerate Swagger docs:

```bash
php artisan l5-swagger:generate
```

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Structure

- **Unit Tests**: Located in `tests/Unit/`
  - `MessageServiceTest.php` - Service layer tests

- **Feature Tests**: Located in `tests/Feature/`
  - `MessageApiTest.php` - API endpoint tests

## 🏗️ Architecture

### Design Patterns

This project follows Laravel best practices and implements several design patterns:

#### Repository Pattern
```
app/
├── Contracts/
│   └── MessageRepositoryInterface.php
└── Repositories/
    └── MessageRepository.php
```

#### Service Layer
```
app/
└── Services/
    └── MessageService.php
```

#### Job/Worker Pattern
```
app/
└── Jobs/
    └── SendMessageJob.php
```

### Project Structure

```
message-system/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── ProcessMessages.php
│   ├── Contracts/
│   │   └── MessageRepositoryInterface.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── MessageController.php
│   ├── Jobs/
│   │   └── SendMessageJob.php
│   ├── Models/
│   │   └── Message.php
│   ├── Repositories/
│   │   └── MessageRepository.php
│   └── Services/
│       └── MessageService.php
├── database/
│   ├── factories/
│   │   └── MessageFactory.php
│   ├── migrations/
│   │   └── xxxx_create_messages_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── MessageSeeder.php
├── routes/
│   └── api.php
└── tests/
    ├── Feature/
    │   └── MessageApiTest.php
    └── Unit/
        └── MessageServiceTest.php
```

## 🔄 Queue System

### How It Works

1. **Command Execution**: `php artisan messages:process`
   - Fetches 2 unsent messages
   - Dispatches jobs to the queue

2. **Queue Worker**: `php artisan queue:work`
   - Processes jobs from the queue
   - Applies rate limiting (2 messages per 5 seconds)
   - Sends messages to webhook endpoint

3. **Message Processing**:
   - Validates message content (max 1000 characters)
   - Sends HTTP request to webhook
   - Updates database with message_id and sent_at
   - Caches message data in Redis

## 🛠️ Troubleshooting

### Common Issues

**Issue: Queue worker not processing jobs**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Restart queue worker
php artisan queue:restart
```

**Issue: Redis connection error**
```bash
# Check if Redis is running
redis-cli ping
# Should return: PONG

# Start Redis if not running
redis-server
```

## 📝 Development

### Adding New Features

1. Create feature branch
2. Implement changes following the existing architecture
3. Write tests
4. Update API documentation
5. Submit pull request

### Code Style

This project follows PSR-12 coding standards. Format code using:

```bash
./vendor/bin/pint
```

## 📄 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Author

Developed as part of the Insider One Software Engineer Assessment Project.

## 🙏 Acknowledgments

- Laravel Framework
- Swagger/OpenAPI
- Redis
- PHPUnit
