<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeTraitCommand extends Command
{
    protected $signature = 'make:trait {name : The name of the trait}
                          {--path=app/Traits : The path where the trait will be created}';

    protected $description = 'Create a new trait';

    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->option('path');

        // Ensure the path exists
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true);
        }

        // Add 'Trait' suffix if not present
        if (!str_ends_with($name, 'Trait')) {
            $name = $name . 'Trait';
        }

        $filePath = $path . '/' . $name . '.php';

        // Generate namespace based on path
        $namespace = str_replace('/', '\\', ucfirst($path));

        // Create trait content
        $content = <<<EOT
<?php

namespace {$namespace};

trait {$name}
{
    //
}
EOT;

        // Create the file
        if (!File::exists($filePath)) {
            File::put($filePath, $content);
            $this->info("Trait created successfully at: {$filePath}");
        } else {
            $this->error("Trait already exists at: {$filePath}");
        }
    }
}
