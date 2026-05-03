<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Artisan command to generate a complete layered architecture for a given entity.
 * 
 * This command creates:
 * - Repository Interface
 * - Eloquent Repository Implementation
 * - Service Class
 * - API Controller
 * - Form Request Classes (Store/Update)
 * - Binds the repository in AppServiceProvider
 * 
 * Usage: php artisan make:layers User --model=User
 * 
 * @package App\Console\Commands
 */
class MakeLayers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:layers 
                            {name : Base name, example User}
                            {--model= : Model name if different}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Generate Repository Interface, Eloquent Repository, Service, API Controller, and bind them in AppServiceProvider';

    /**
     * The base name extracted from the command argument.
     */
    private string $baseName;

    /**
     * The model name (may differ from base name if specified via option).
     */
    private string $modelName;

    /**
     * Array of file paths for different directories and files.
     */
    private array $filePaths = [];

    /**
     * Array of class names for generated files.
     */
    private array $classNames = [];

    /**
     * Execute the console command.
     * 
     * This method orchestrates the entire file generation process:
     * 1. Initialize class names and file paths
     * 2. Create necessary directories
     * 3. Generate all required files
     * 4. Bind repository in service provider
     * 5. Clean up and optimize
     * 
     * @return int Command exit code
     */
    public function handle(): int
    {
        $this->initializeClassNames();
        $this->setupDirectories();
        $this->generateAllFiles();
        $this->bindInServiceProvider();
        $this->cleanup();

        $this->info('Layers generated successfully.');
        return self::SUCCESS;
    }

    /**
     * Initialize class names and file paths based on the provided arguments.
     * 
     * Sets up the base name, model name, and generates all the necessary
     * class names and directory paths for the layered architecture.
     */
    private function initializeClassNames(): void
    {
        $this->baseName = trim($this->argument('name'));
        $this->modelName = $this->option('model') ?: $this->baseName;

        // Generate all class names needed for the layered architecture
        $this->classNames = [
            'repository' => "{$this->baseName}Repository",
            'interface' => "{$this->baseName}RepositoryInterface",
            'service' => "{$this->baseName}Service",
            'controller' => "{$this->baseName}Controller",
            'storeRequest' => "Store{$this->baseName}Request",
            'updateRequest' => "Update{$this->baseName}Request",
        ];

        // Define all file paths for directories and the service provider
        $this->filePaths = [
            'interfaceDir' => app_path('Repositories/Interfaces'),
            'repositoryDir' => app_path('Repositories/Eloquent'),
            'serviceDir' => app_path('Services'),
            'controllerDir' => app_path('Http/Controllers/Api'),
            'requestDir' => app_path("Http/Requests/Api/{$this->baseName}"),
            'providerPath' => app_path('Providers/AppServiceProvider.php'),
        ];
    }

    /**
     * Create all necessary directories for the layered architecture.
     * 
     * Ensures that all required directories exist before file generation.
     * Uses Laravel's File facade to create directories if they don't exist.
     */
    private function setupDirectories(): void
    {
        foreach (['interfaceDir', 'repositoryDir', 'serviceDir', 'controllerDir', 'requestDir'] as $dir) {
            File::ensureDirectoryExists($this->filePaths[$dir]);
        }
    }

    /**
     * Coordinate the generation of all required files.
     * 
     * Calls each individual file generation method in the correct order
     * to create the complete layered architecture.
     */
    private function generateAllFiles(): void
    {
        $this->generateRepositoryInterface();
        $this->generateRepository();
        $this->generateService();
        $this->generateStoreRequest();
        $this->generateUpdateRequest();
        $this->generateController();
    }

    /**
     * Generate the repository interface file.
     * 
     * Creates the interface that defines the contract for repository operations.
     * The interface includes standard CRUD methods and any custom operations.
     */
    private function generateRepositoryInterface(): void
    {
        $path = "{$this->filePaths['interfaceDir']}/{$this->classNames['interface']}.php";
        $content = $this->buildStub('repository-interface.stub', [
            'interfaceName' => $this->classNames['interface'],
            'modelName' => $this->modelName,
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Generate the Eloquent repository implementation file.
     * 
     * Creates the concrete implementation of the repository interface
     * using Laravel's Eloquent ORM for database operations.
     */
    private function generateRepository(): void
    {
        $path = "{$this->filePaths['repositoryDir']}/{$this->classNames['repository']}.php";
        $content = $this->buildStub('repository-eloquent.stub', [
            'className' => $this->classNames['repository'],
            'interfaceName' => $this->classNames['interface'],
            'modelName' => $this->modelName,
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Generate the service class file.
     * 
     * Creates the service layer that handles business logic and acts
     * as an intermediary between controllers and repositories.
     */
    private function generateService(): void
    {
        $path = "{$this->filePaths['serviceDir']}/{$this->classNames['service']}.php";
        $content = $this->buildStub('service.stub', [
            'serviceName' => $this->classNames['service'],
            'interfaceName' => $this->classNames['interface'],
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Generate the store form request class.
     * 
     * Creates a form request class for validating incoming data
     * when creating new resources.
     */
    private function generateStoreRequest(): void
    {
        $path = "{$this->filePaths['requestDir']}/{$this->classNames['storeRequest']}.php";
        $content = $this->buildStub('storerequest.stub', [
            'storeRequestName' => $this->classNames['storeRequest'],
            'modelName' => $this->modelName,
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Generate the update form request class.
     * 
     * Creates a form request class for validating incoming data
     * when updating existing resources.
     */
    private function generateUpdateRequest(): void
    {
        $path = "{$this->filePaths['requestDir']}/{$this->classNames['updateRequest']}.php";
        $content = $this->buildStub('updaterequest.stub', [
            'updateRequestName' => $this->classNames['updateRequest'],
            'modelName' => $this->modelName,
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Generate the API controller class.
     * 
     * Creates the controller that handles HTTP requests and responses
     * for the API endpoints, using the service layer for business logic.
     */
    private function generateController(): void
    {
        $path = "{$this->filePaths['controllerDir']}/{$this->classNames['controller']}.php";
        $content = $this->buildStub('controllerrepositoryapi.stub', [
            'controllerName' => $this->classNames['controller'],
            'serviceName' => $this->classNames['service'],
            'modelName' => $this->modelName,
            'storeRequestName' => $this->classNames['storeRequest'],
            'updateRequestName' => $this->classNames['updateRequest'],
        ]);
        $this->writeFile($path, $content);
    }

    /**
     * Perform cleanup operations after file generation.
     * 
     * Clears Laravel's cache and optimizes the application
     * to ensure the new files are properly loaded.
     */
    private function cleanup(): void
    {
        $this->call('optimize:clear');
    }

    /**
     * Write content to a file with proper error handling.
     * 
     * Checks if the file already exists and respects the --force option.
     * Provides appropriate feedback to the user about the action taken.
     * 
     * @param string $path The file path to write to
     * @param string $content The content to write to the file
     */
    protected function writeFile(string $path, string $content): void
    {
        $exists = File::exists($path);

        if ($exists && ! $this->option('force')) {
            $this->warn("Skipped (already exists): {$path}");
            return;
        }

        File::put($path, $content);

        $this->info(($exists ? 'Updated' : 'Created') . ": {$path}");
    }

    /**
     * Build file content from a stub template.
     * 
     * Loads a stub file and replaces placeholder values with
     * the provided replacements array.
     * 
     * @param string $stubName The name of the stub file
     * @param array $replacements Associative array of placeholder replacements
     * @return string The processed stub content
     * @throws \Exception If the stub file is not found
     */
    protected function buildStub(string $stubName, array $replacements): string
    {
        $stubPath = base_path("stubs/{$stubName}");

        if (! File::exists($stubPath)) {
            $this->error("Stub not found: {$stubPath}");
            exit(self::FAILURE);
        }

        $stub = File::get($stubPath);

        // Replace all {{key}} placeholders with corresponding values
        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{' . $key . '}}', $value, $stub);
        }

        return $stub;
    }

    /**
     * Bind the repository interface to its implementation in AppServiceProvider.
     * 
     * This method modifies the AppServiceProvider to register the repository
     * binding, enabling dependency injection for the repository pattern.
     * The binding allows the interface to be automatically resolved to the
     * concrete Eloquent implementation.
     */
    private function bindInServiceProvider(): void
    {
        $providerPath = $this->filePaths['providerPath'];
        
        if (!File::exists($providerPath)) {
            $this->warn("AppServiceProvider not found: {$providerPath}");
            return;
        }

        $content = File::get($providerPath);
        $content = $this->addInterfaceUseStatement($content);
        $content = $this->addRepositoryUseStatement($content);
        $content = $this->addBindingToRegisterMethod($content);

        File::put($providerPath, $content);
        $this->info('Binding added to AppServiceProvider.');
    }

    /**
     * Add the repository interface use statement to AppServiceProvider.
     * 
     * Adds the import statement for the repository interface if it
     * doesn't already exist in the file.
     * 
     * @param string $content The current content of AppServiceProvider
     * @return string The modified content with interface use statement
     */
    private function addInterfaceUseStatement(string $content): string
    {
        $interfaceFqn = "App\\Repositories\\Interfaces\\{$this->classNames['interface']}";
        $useStatement = "use {$interfaceFqn};";

        if (str_contains($content, $useStatement)) {
            return $content;
        }

        return preg_replace(
            '/^namespace\s+App\\\\Providers;\s*$/m',
            "namespace App\\Providers;\n\n{$useStatement}",
            $content,
            1
        );
    }

    /**
     * Add the repository implementation use statement to AppServiceProvider.
     * 
     * Adds the import statement for the concrete repository implementation
     * if it doesn't already exist in the file.
     * 
     * @param string $content The current content of AppServiceProvider
     * @return string The modified content with repository use statement
     */
    private function addRepositoryUseStatement(string $content): string
    {
        $repositoryFqn = "App\\Repositories\\Eloquent\\{$this->classNames['repository']}";
        $useStatement = "use {$repositoryFqn};";

        if (str_contains($content, $useStatement)) {
            return $content;
        }

        $interfaceUseStatement = "use App\\Repositories\\Interfaces\\{$this->classNames['interface']};";
        return preg_replace(
            '/(' . preg_quote($interfaceUseStatement, '/') . '\n?)/',
            "$1{$useStatement}\n",
            $content,
            1
        );
    }

    /**
     * Add the repository binding to the register method of AppServiceProvider.
     * 
     * Adds the Laravel service container binding that maps the repository
     * interface to its concrete implementation, enabling dependency injection.
     * 
     * @param string $content The current content of AppServiceProvider
     * @return string The modified content with binding added to register method
     */
    private function addBindingToRegisterMethod(string $content): string
    {
        $bindingLine = "\$this->app->bind({$this->classNames['interface']}::class, {$this->classNames['repository']}::class);";

        if (str_contains($content, $bindingLine)) {
            return $content;
        }

        return preg_replace(
            '/public function register\(\): void\s*\{\s*/',
            "public function register(): void\n    {\n        {$bindingLine}\n        ",
            $content,
            1
        );
    }
}
