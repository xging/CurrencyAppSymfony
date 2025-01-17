<?php
// require_once __DIR__ . '/../vendor/autoload.php';

// use App\Services\Http\HttpKernel;
// use App\Services\Console\ConsoleKernel;
// use Psr\Log\LoggerInterface;
// use App\Model\Database\DatabaseInterface;
// use Doctrine\ORM\EntityManagerInterface;
// class test
// {
//     private EntityManagerInterface $em;
//     private LoggerInterface $logger;

//     public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
//     {
//         $this->em = $em;
//         $this->logger = $logger;
//     }
//     public function handle(array $argv): void
//     {
//         if (PHP_SAPI === 'cli') {
//             // Console
//             $kernel = new ConsoleKernel($this->em, $this->logger);
//             $kernel->handle($argv);
//         } else {
//             // HTTP
//             $kernel = new HttpKernel();
//             $kernel->handle($_SERVER['REQUEST_URI']);
//         }
        
//     }
// }
// // Инициализация и вызов метода
// $test = new Test($em, $logger);
// $test->handle($argv);