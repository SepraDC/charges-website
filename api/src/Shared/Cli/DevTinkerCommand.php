<?php

namespace App\Shared\Cli;

use App\Tests\Support\Helper\DataFactory\AdvancedFactoryMuffin;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Faker\Factory;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Stores\RepositoryStore;
use Psy\Configuration;
use Psy\Shell;
use Psy\VersionUpdater\Checker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'dev:tinker',
    description: 'Interact with application',
    aliases: ['dev:faker:entity', 'tinker'],
)]
class DevTinkerCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly ManagerRegistry $registry,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->em->getConnection()->getConfiguration();
        $configuration->setSQLLogger(null);
        $io = new SymfonyStyle($input, $output);
        if ($input->getArgument('command') === 'dev:faker:entity') {
            $io->warning("The `dev:faker:entity` alias is deprecated and will be removed. Use `dev:tinker` or `tinker` instead");
        }
        $metas = $this->registry->getManager()->getMetadataFactory()->getAllMetadata();
        $store = new RepositoryStore($this->em);
        $factoryMuffin = new AdvancedFactoryMuffin($store);
        $factoryMuffin->loadFactories($this->parameterBag->get('kernel.project_dir') . "/tests/Support/factories");

        $io->table(
            ['variable', ''],
            [
                ['$I', 'Codeception\'s DataFactory-like object'],
                ['$fm', 'FactoryMuffin instance'],
                ['$em', 'EntityManager instance'],
                ['$faker', 'Faker Generator instance'],
            ]
        );

        $shell = new Shell();
        $config = Configuration::fromInput($input);
        $config->setUpdateCheck(Checker::NEVER);

        foreach ($metas as $meta) {
            if (!class_exists($meta->getName())) {
                continue;
            }
            $shell->addCode(sprintf("use %s;", $meta->getName()));
        }
        $shell->addCode(sprintf("use %s;", Carbon::class));

        $shell->setScopeVariables([
            'fm' => $factoryMuffin,
            'em' => $this->em,
            'faker' => Factory::create(),
            'I' => new class($factoryMuffin) {
                public function __construct(private readonly FactoryMuffin $fm)
                {
                }

                public function have(string $name, array $extraAttrs = []): object
                {
                    return $this->fm->create($name, $extraAttrs);
                }

                public function haveMultiple(string $name, int $times, array $extraAttrs = []): array
                {
                    return $this->fm->seed($times, $name, $extraAttrs);
                }

            },
        ]);
        $shell->run();
        $this->em->flush();
        $io->success('Entites were created');


        return Command::SUCCESS;
    }

    public function isEnabled(): bool
    {
        return $this->parameterBag->get('kernel.debug');
    }
}
