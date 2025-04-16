# Gimli PHP Framework - Coding Rules

## General Conventions

1. **Type Declaration**: Always use `declare(strict_types=1);` at the beginning of PHP files.
2. **Namespaces**: Use PascalCase with backslashes for namespaces (e.g., `Gimli\Http\Request`).
3. **Variable Naming**: Use snake_case for variables (e.g., `$user_data`, `$invoice_id`).
4. **Method Naming**: Use camelCase for method names (e.g., `getQueryParam()`, `setResponse()`).
5. **Class Naming**: Class names should match their file name with the format of `Name_Type` (e.g., `User_Controller`, `Invoice_Model`).
6. **File Organization**: Group related functionality into directories (e.g., Controllers, Models, Logic).

## Application Structure

### Main Components

1. **Controllers**: Handle HTTP requests and return appropriate responses.
   - Located in `App/Controllers/`
   - Named as `Entity_Controller.php`
   - Should be thin, primarily delegating to Logic classes

2. **Models**: Represent database tables and handle data persistence.
   - Located in `App/Models/`
   - Extend `Gimli\Database\Model`
   - Define table name, primary key, and properties
   - Use lifecycle hooks like `beforeSave()` and `afterSave()`

3. **Logic**: Contain business logic and data manipulation.
   - Located in `App/Logic/`
   - Named as `Entity_Logic.php`
   - Return `Logic_Response` objects

4. **Views**: Templates using Latte template engine.
   - Located in `App/views/`
   - Use `.latte` extension

5. **Routes**: Define application endpoints.
   - Located in `App/Routes/`
   - Separated into web.php, api.php, and cli.php

6. **Middlewares**: Process requests before they reach controllers.
   - Located in `App/Middlewares/`
   - Implement `Middleware_Interface`

## Controllers

1. Controllers should be thin and primarily:
   - Parse request data
   - Call business logic
   - Return appropriate response

```php
class Entity_Controller
{
    public function __construct(
        public Latte_Engine $Latte_Engine,
    ) {}

    public function index(Response $Response, Request $Request, Entity_Logic $Entity_Logic): Response {
        // Call logic methods
        $data = $Entity_Logic->loadAll();
        
        // Return response
        return $Response->setResponse(
            $this->Latte_Engine->render('path/to/template.latte', $data)
        );
    }
}
```

## Models

1. Models should extend `Gimli\Database\Model`
2. Define table name and primary key
3. Define public properties that match database columns
4. Use lifecycle hooks for pre/post-processing

```php
class Entity_Model extends Model
{
    protected string $table_name = 'entity_table';
    protected string $primary_key = 'id';
    
    public int $id;
    public string $unique_id;
    public string $name;
    public string $created_at;
    public string $updated_at;
    
    public function beforeSave(): void {
        $this->created_at = $this->created_at ?? gmdate('Y-m-d H:i:s');
        $this->updated_at = gmdate('Y-m-d H:i:s');
    }
}
```

## Model Operations

Models in Gimli are an object-oriented representation of database tables, providing an easy and type-safe way to interact with your database.

### Model Structure

1. **Table Definition**:
   - Define the table name and primary key at the beginning of your model
   ```php
   protected string $table_name = 'users';
   protected string $primary_key = 'id';
   ```

2. **Property Declarations**:
   - Declare public properties with correct types matching database columns
   - This provides type safety and IDE autocomplete support
   ```php
   public int $id;
   public string $email;
   public int $is_active;
   ```

3. **Lifecycle Hooks**:
   - `beforeSave()`: Called before saving a model to database (create or update)
   - `afterSave()`: Called after a model is saved
   - `afterLoad()`: Called after a model is loaded from the database
   ```php
   public function beforeSave(): void {
       $this->created_at = $this->created_at ?? gmdate('Y-m-d H:i:s');
       $this->updated_at = gmdate('Y-m-d H:i:s');
       $this->unique_id = $this->unique_id ?? Key_Maker::uniqueId();
   }
   ```

### Basic Model Operations

1. **Loading Data**:
   - Load a record by using the `load()` method with a WHERE condition:
   ```php
   // Load by primary key
   $User_Model->load('id = :id', ['id' => 123]);
   
   // Load by other criteria
   $User_Model->load('email = :email', ['email' => 'user@example.com']);
   ```

2. **Checking if Loaded**:
   - Use `isLoaded()` to verify a record was found:
   ```php
   if ($User_Model->isLoaded()) {
       // Record exists, model has data
   } else {
       // Record not found
   }
   ```

3. **Creating Records**:
   - Set properties and call `save()`:
   ```php
   $User_Model->reset(); // Clear any loaded data
   $User_Model->email = 'new@example.com';
   $User_Model->name = 'New User';
   $User_Model->save(); // Inserts new record
   ```

4. **Updating Records**:
   - Load the record, modify properties, then save:
   ```php
   if ($User_Model->load('id = :id', ['id' => 123])) {
       $User_Model->email = 'updated@example.com';
       $User_Model->save(); // Updates existing record
   }
   ```

5. **Resetting a Model**:
   - Clear all properties and loaded state:
   ```php
   $User_Model->reset();
   ```

6. **Getting Model Data as Array**:
   - Use `getData()` to get all model properties as an array:
   ```php
   $data = $User_Model->getData();
   ```

7. **Loading from Data Set**:
   - Load model properties from an array:
   ```php
   $User_Model->loadFromDataSet([
       'id' => 1,
       'email' => 'test@example.com',
       'name' => 'Test User'
   ]);
   ```

## Database Class

The Gimli Database class provides direct database access when you need more control than Models provide. It's typically used within Logic classes for complex queries, transactions, or operations involving multiple tables.

### Connection Handling

The Database class automatically manages PDO connections based on your configuration settings.

```php
// The Database class is typically injected
public function __construct(
    protected Database $Database
) {}
```

### Query Operations

1. **Basic Query Execution**:
   ```php
   $success = $this->Database->execute(
       'UPDATE users SET last_login = NOW() WHERE id = :id',
       ['id' => 123]
   );
   ```

2. **Fetching Results**:
   - Fetch all matching rows:
   ```php
   $users = $this->Database->fetchAll(
       'SELECT * FROM users WHERE status = :status',
       ['status' => 'active']
   );
   ```
   
   - Fetch a single row:
   ```php
   $user = $this->Database->fetchRow(
       'SELECT * FROM users WHERE email = :email',
       ['email' => 'user@example.com']
   );
   ```
   
   - Fetch a single column value:
   ```php
   $count = $this->Database->fetchColumn(
       'SELECT COUNT(*) FROM users WHERE is_active = :is_active',
       ['is_active' => 1]
   );
   ```
   
   - Stream large result sets with a generator:
   ```php
   $rows = $this->Database->yieldRows(
       'SELECT * FROM large_table WHERE created_at > :date',
       ['date' => '2023-01-01']
   );
   
   foreach ($rows as $row) {
       // Process each row without loading all into memory
   }
   ```

3. **Insert Operations**:
   ```php
   $this->Database->insert('users', [
       'email' => 'new@example.com',
       'name' => 'New User',
       'created_at' => gmdate('Y-m-d H:i:s')
   ]);
   
   $user_id = $this->Database->lastInsertId();
   ```

4. **Update Operations**:
   ```php
   $this->Database->update(
       'users',
       'id = :id',
       ['email' => 'updated@example.com', 'name' => 'Updated Name'],
       ['id' => 123]
   );
   ```

### Helper Functions

Gimli provides helper functions for common database operations, which can be used anywhere in your code:

```php
use function Gimli\Database\fetch_all;
use function Gimli\Database\fetch_row;
use function Gimli\Database\fetch_column;
use function Gimli\Database\row_exists;

// Get multiple rows
$active_users = fetch_all('SELECT * FROM users WHERE is_active = :status', ['status' => 1]);

// Get a single row
$user = fetch_row('SELECT * FROM users WHERE id = :id', ['id' => 123]);

// Get a single value
$count = fetch_column('SELECT COUNT(*) FROM users');

// Check if a record exists
$exists = row_exists('SELECT 1 FROM users WHERE email = :email', ['email' => 'test@example.com']);
```

### Transactions

For operations that require multiple related database changes, use transactions to ensure data integrity:

```php
try {
    $this->Database->execute('BEGIN');
    
    // Multiple database operations
    $this->Database->insert('orders', ['user_id' => $user_id, 'total' => $total]);
    $order_id = $this->Database->lastInsertId();
    
    foreach ($items as $item) {
        $this->Database->insert('order_items', [
            'order_id' => $order_id,
            'product_id' => $item['id'],
            'quantity' => $item['qty']
        ]);
    }
    
    $this->Database->execute('COMMIT');
    return true;
} catch (Exception $e) {
    // Rollback on any error
    $this->Database->execute('ROLLBACK');
    throw $e;
}
```

### Best Practices

1. **Parameter Binding**:
   - Always use parameterized queries with named parameters (`:param_name`)
   - Never concatenate values directly into SQL strings

2. **Query Organization**:
   - For complex queries, use HEREDOC syntax for better readability
   ```php
   $sql = <<<SQL
       SELECT u.*, 
              p.name as plan_name
       FROM users u
       JOIN plans p ON u.plan_id = p.id
       WHERE u.status = :status
       ORDER BY u.created_at DESC
   SQL;
   ```

3. **Error Handling**:
   - Use try/catch blocks to handle database exceptions
   - Consider implementing a logging strategy for database errors

4. **Logic Layer**:
   - Keep direct database operations within Logic classes
   - Don't use the Database class directly in Controllers

5. **Efficiency**:
   - Use `yieldRows()` for large result sets to prevent memory issues
   - Select only the columns you need rather than using `SELECT *`

## Logic Classes

1. Logic classes contain business logic
2. Return `Logic_Response` objects (Not framework provided)
3. Should validate input data
4. Handle database operations through Models

`Logic_Response` class is not provided by the framework but is commonly added as a utility with the following structure:
```php
declare(strict_types=1);

namespace App\Utilities;

class Logic_Response
{
	/**
	 * Success Flag
	 * 
	 * @var bool
	 */
	public bool $success;

	/**
	 * Message
	 * 
	 * @var string
	 */
	public string $message;

	/**
	 * Data
	 * 
	 * @var null|array
	 */
	public null|array $data;

	/**
	 * Constructor
	 *
	 * @param bool       $success Success Flag
	 * @param string     $message Message
	 * @param null|array $data    Data
	 */
	public function __construct(
		bool $success = TRUE, 
		string $message = 'Success', 
		null|array $data = NULL
	) {
		$this->success = $success;
		$this->message = $message;
		$this->data    = $data;
	}
}
```

```php
class Entity_Logic
{
    public function __construct(
        protected Session $Session,
        protected Entity_Model $Entity_Model,
    ) {}
    
    public function save(array $data): Logic_Response {
        // Validate data
        if (!$this->isValid($data)) {
            return new Logic_Response(
                success: false,
                message: 'Invalid data provided.'
            );
        }
        
        // Process data and save
        $this->Entity_Model->property = $data['property'];
        $this->Entity_Model->save();
        
        return new Logic_Response(
            success: true,
            data: $this->Entity_Model->getData()
        );
    }
}
```

## Routing

1. Routes are defined in dedicated files (`web.php`, `api.php`, `cli.php`)
2. Use the `Route` class to define routes
3. Support for different HTTP methods (GET, POST, PUT, DELETE)
4. Group related routes
5. Apply middlewares to routes or groups

```php
// Simple route
Route::get('/path', Controller::class);

// Route with controller method specified
Route::post('/path', [Controller::class, 'methodName']);

// Route with parameter
Route::get('/path/:alphanumeric#param_name', [Controller::class, 'method']);

// Route group with middleware
Route::group('/prefix', function() {
    Route::get('/subpath', [Controller::class, 'method']);
}, [Middleware::class]);
```

## Dependency Injection

1. Use constructor injection for dependencies
2. Use the `resolve()` function to get instances when needed
3. Use `resolve_fresh()` to get a new instance

```php
use function Gimli\Injector\resolve;

// In a method
$dependency = resolve(DependencyClass::class);

// Constructor injection
public function __construct(
    protected Dependency $Dependency,
) {}
```

## Template System

1. Use Latte templating engine
2. Templates are located in `App/views/`
3. Use the `render()` function or `Latte_Engine->render()` to render templates

```php
// In a controller
return $Response->setResponse(
    $this->Latte_Engine->render('path/to/template.latte', $data)
);

// Using the helper function
use function Gimli\View\render;
$html = render('path/to/template.latte', $data);
```

## Response Handling

1. Always return a `Response` object from controller methods
2. Use appropriate response methods:
   - `setResponse()` for HTML responses
   - `setJsonResponse()` for JSON responses
   - Set proper status codes when needed

```php
// HTML response
return $Response->setResponse(
    $this->Latte_Engine->render('template.latte', $data)
);

// JSON response
return $Response->setJsonResponse(
    success: true,
    body: $data
);

// Error response
return $Response->setResponse(
    response_body: 'Not Found',
    response_code: 404
);
```

## CSRF Protection

CSRF (Cross-Site Request Forgery) protection is implemented via the `Gimli\View\Csrf` class. This system helps prevent unauthorized requests from other domains.

### Implementation Details

1. **Token Generation**:
   - The system generates cryptographically secure random tokens using `bin2hex(random_bytes(32))`
   - Each token is stored in the user's session with an expiration time (15 minutes by default)
   - Multiple tokens can be active simultaneously for different forms

2. **Adding CSRF to Forms**:
   - Use the `{csrf()}` Latte macro in your templates to add a hidden input field:
   ```html
   <form method="post">
       {csrf()}
       <!-- form fields -->
   </form>
   ```
   - This generates: `<input type='hidden' name='csrf_token' value='generated-token'>`

3. **Manual Token Generation**:
   - Generate a token directly with `Csrf::generate()` when needed
   - This is useful for AJAX requests or custom implementations

4. **Token Verification**:
   - Always verify the token in logic classes before processing form data:
   ```php
   use Gimli\View\Csrf;
   
   if (Csrf::verify($data['csrf_token']) === false) {
       return new Logic_Response(
           success: false,
           message: 'Invalid CSRF token. Please try again.'
       );
   }
   ```

5. **Token Lifecycle**:
   - Tokens are single-use; they're deleted from the session after verification
   - Expired tokens are automatically invalidated
   - Token validation failures may indicate a CSRF attack or token expiration

6. **Best Practices**:
   - Include CSRF verification in all state-changing operations
   - Always verify tokens at the beginning of processing to avoid partial execution
   - Return user-friendly error messages when verification fails

7. **AJAX Requests**:
   - For AJAX forms, include the token in the form data
   - For programmatic AJAX requests, get a token via JavaScript and include it in the request payload

## Event System

The Gimli PHP framework includes a powerful event system that enables loose coupling between components through an event-driven architecture.

### Core Components

1. **Event_Manager**:
   - Central hub for event handling
   - Manages event subscriptions
   - Dispatches events to subscribers
   - Available through dependency injection

2. **Event Interface**:
   - Implemented by event listener classes
   - Requires an `execute()` method that handles event processing

3. **Event Attribute**:
   - PHP 8 attribute for declarative event subscription
   - Can be applied to classes implementing Event_Interface
   - Supports multiple event subscriptions per class

### Publishing Events

1. **Function-based approach**:
   ```php
   use function Gimli\Events\publish_event;
   
   // Trigger an event with optional data
   publish_event('user_login', ['user_id' => 123, 'timestamp' => time()]);
   ```

2. **Direct Event_Manager usage**:
   ```php
   $Event_Manager = resolve(Event_Manager::class);
   $Event_Manager->publish('event_name', ['data' => $value]);
   ```

### Subscribing to Events

1. **Attribute-based subscription**:
   ```php
   use Gimli\Events\Event;
   use Gimli\Events\Event_Interface;
   
   #[Event('user_login')]
   #[Event('user_logout')]
   class User_Session_Event implements Event_Interface
   {
       public function execute(string $event_name, array $args = []): void {
           if ($event_name === 'user_login') {
               // Handle login event
           } else if ($event_name === 'user_logout') {
               // Handle logout event
           }
       }
   }
   ```

2. **Programmatic subscription**:
   ```php
   use function Gimli\Events\subscribe_event;
   
   // Subscribe using a callable
   subscribe_event('user_login', function(string $event, array $data) {
       // Handle the event
   });
   
   // Subscribe using a class name (must implement Event_Interface)
   subscribe_event('user_login', User_Session_Event::class);
   ```

3. **Class registration with reflection**:
   ```php
   $Event_Manager = resolve(Event_Manager::class);
   
   // Register all event listeners in an array
   $Event_Manager->register([
       User_Session_Event::class,
       Free_Trial_Event::class,
       // Other event listener classes
   ]);
   ```
4. **Config Registration**:
Registration can be handled in `App/Core/Config.php` under the `events` array key, this is the preferred method because Gimli will handle subscribing to those events.
```php
// App/Core/Config.php
// other config array settings
    'events' => [
        User_Session_Event::class,
        Free_Trial_Event::class,
    ],
```

### Best Practices

1. **Event Naming**:
   - Use lowercase with dots or underscores as separators
   - Follow a consistent naming pattern (e.g., `entity.action`)
   - Examples: `user.login`, `user.logout`, `invoice.created`

2. **Event Data**:
   - Pass all relevant data as an associative array
   - Include identifiers to allow listeners to fetch additional data if needed
   - Avoid circular references in event data

3. **Event Listeners**:
   - Keep listeners focused on a single responsibility
   - Handle multiple related events in the same listener when appropriate
   - Use dependency injection in listener classes

4. **Common Event Types**:
   - System events: `gimli.application.start`, `gimli.application.end`
   - User events: `user_login`, `user_logout`
   - Model events: `model.created`, `model.updated`, `model.deleted`

5. **Performance Considerations**:
   - Register only necessary event listeners
   - Keep event handlers lightweight
   - Use async processing for intensive operations

## CSS and JavaScript

1. Use Tailwind CSS for styling
2. Define Tailwind configuration in `tailwind.config.js`
3. Alpine.js can be used for interactive components

## Error Handling

1. Use proper HTTP status codes
2. Return meaningful error messages
3. Use try/catch blocks for exception handling
4. Custom exceptions should extend from appropriate base exceptions

## Security Best Practices

1. Always validate and sanitize user input
2. Use CSRF protection for forms
3. Use parameterized queries for database operations
4. Hash passwords securely
5. Use proper session handling

## Configuration

1. Configuration values should be accessed through the Config class
2. Use environment variables when appropriate
3. Use the helper function to access config values:

```php
use function Gimli\Environment\get_config_value;
$value = get_config_value('config_key');
```

## Database Operations

1. Use Models for database operations
2. Use the Database class for raw queries if needed
3. Use transactions for multiple operations
4. Properly sanitize and validate data before inserting into the database 