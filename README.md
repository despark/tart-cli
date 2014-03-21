### Jam Tart CLI

#### Install

Install using Composer with:
``` bash
composer require --dev despark/tart-cli
```

#### Setup

To add to your Symfony console application:

``` php
// Use needed classes and commands to load into the Console Application
use Symfony\Component\Console\Application;
use Despark\Command\Tart\GenerateCommand;
use Despark\Command\Tart\DeleteCommand;

// Create a new Console Application instance
$application = new Application();

// Attach commands to the Console Application instance
$application->add(new GenerateCommand);
$application->add(new DeleteCommand);

// Run the Console Application instance
$application->run();
```
