<?php

namespace RandomFakerBundle\Command;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use Faker\Factory;
use Override;
use RandomFakerBundle\Helper\FixtureHelper;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'RandomFixturesCommand',
    description: 'Load random fixtures with Faker',
    aliases    : ['fixtures:random']
)]
class RandomFixturesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly FixtureHelper $fixtureHelper)
    {
        parent::__construct(AsCommand::class);
    }

    /**
     * @throws MappingException
     * @throws ReflectionException
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker         = Factory::create('fr_FR');
        $entities      = [];
        $uniqueColumns = [];
        $metaData      = $this->entityManager->getMetadataFactory()->getAllMetadata();

        usort($metaData, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

        foreach ($metaData as $meta) {
            $reflectionClass = new ReflectionClass($meta->getName());
            $yamlFile        = $this->fixtureHelper->getYamlFile($reflectionClass);

            if ($yamlFile['fileExists']) {
                $entities[$reflectionClass->getShortName()] = [
                    'associations' => [],
                    'columns'      => [],
                    'name'         => $reflectionClass->getName(),
                    'records'      => $this->fixtureHelper->getRecords($reflectionClass),
                    'order'        => $this->fixtureHelper->getOrder($reflectionClass),
                    'table'        => $meta->getTableName()
                ];

                foreach ($meta->getAssociationMappings() as $fieldName => $mapping) {
                    $joinColumns = $mapping['joinColumns'] ?? [];

                    $entities[$reflectionClass->getShortName()]['associations'][$fieldName] = [
                        'fields'       => $this->fixtureHelper->getFields($reflectionClass, $fieldName),
                        'nullable'     => $this->fixtureHelper->isAssociationNullable($reflectionClass, $fieldName, $joinColumns),
                        'type'         => $this->fixtureHelper->getTypeAssociation($mapping['type']),
                        'targetEntity' => $mapping['targetEntity']
                    ];

                    if ($this->fixtureHelper->isAssociationUnique($reflectionClass, $fieldName, $joinColumns)) {
                        $uniqueColumns[$fieldName] = [];
                    }
                }

                foreach ($meta->getFieldNames() as $fieldName) {
                    $entities[$meta->getReflectionClass()->getShortName()]['columns'][$fieldName] = [
                        'fields'         => $this->fixtureHelper->getFields($reflectionClass, $fieldName),
                        'generatedValue' => $this->fixtureHelper->isGeneratedValue($meta->getName(), $fieldName),
                        'length'         => $meta->getFieldMapping($fieldName)['length'] ?? null,
                        'nullable'       => $meta->getFieldMapping($fieldName)['nullable'] ?? false,
                        'precision'      => $meta->getFieldMapping($fieldName)['precision'] ?? null,
                        'type'           => $meta->getFieldMapping($fieldName)['type']
                    ];

                    if ($meta->getFieldMapping($fieldName)['unique'] ?? false) {
                        $uniqueColumns[$fieldName] = [];
                    }
                }
            }
        }

        $entities = array_filter($entities, function ($item) {
            return $item['order'] !== null;
        });

        usort($entities, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        foreach ($entities as $entity) {
            $output->writeln(['Add random fixtures for table "' . $entity['table'] . '"']);

            $datas = [];
            
            foreach ($entity['associations'] as $keyAS => $association) {
                foreach ($association['fields'] as $keyF => $field) {
                    if (isset($field['nameFaker']) && $field['nameFaker'] == 'ignore' && isset($field['parameters']['ignore'])
                        && $field['parameters']['ignore'] === true
                    ) {
                        unset($entity['associations'][$keyAS]);
                    }
                    if (isset($field['nameFaker']) && $field['nameFaker'] == 'nullable' && isset($field['parameters']['nullable'])) {
                        $entity['associations'][$keyAS]['nullable'] = $field['parameters']['nullable'];
                        unset($entity['associations'][$keyAS]['fields'][$keyF]);
                    }
                }
            }

            foreach ($entity['columns'] as $keyC => $column) {
                foreach ($column['fields'] as $keyA => $field) {
                    if (isset($field['nameFaker']) && $field['nameFaker'] == 'ignore' && isset($field['parameters']['ignore'])
                        && $field['parameters']['ignore'] === true
                    ) {
                        unset($entity['columns'][$keyC]);
                    }
                    if (isset($field['nameFaker']) && $field['nameFaker'] == 'nullable' && isset($field['parameters']['nullable'])) {
                        $entity['columns'][$keyC]['nullable'] = $field['parameters']['nullable'];
                        unset($entity['columns'][$keyC]['fields'][$keyA]);
                    }
                }
            }

            foreach ($entity['associations'] as $keyA => $association) {
                if ($association['type'] == $this->fixtureHelper->getTypeAssociation(ClassMetadataInfo::MANY_TO_ONE)) {
                    $datas[0][] = $this->fixtureHelper->camelToSnakeCase($keyA) . '_id';
                }
            }

            foreach ($entity['columns'] as $keyC => $column) {
                $datas[0][] = $this->fixtureHelper->camelToSnakeCase($keyC);
            }

            if ($entity['records'] >= 1) {
                for ($i = 1; $i <= $entity['records']; $i++) {
                    foreach ($entity['associations'] as $association) {
                        if ($association['type'] == $this->fixtureHelper->getTypeAssociation(ClassMetadataInfo::MANY_TO_ONE)) {
                            if ($totalNumberEntity = $this->fixtureHelper->getTotalNumberOfEntity($entities, $association['targetEntity'])) {
                                $datas[$i][] = $association['nullable'] && mt_rand(0, 1) ? null : mt_rand(1, $totalNumberEntity);
                            } else {
                                $datas[$i][] = null;
                            }
                        }
                    }

                    foreach ($entity['columns'] as $keyC => $column) {
                        $datas = $this->addDatas($column, $datas, $faker, $keyC, $i, $output, $uniqueColumns);
                    }
                }

                $this->fixtureHelper->loadFixturesFromCSV($output, $entity['table'], $datas);

                $output->writeln(['']);
                $output->writeln(['Add random fixtures for table "' . $entity['table'] . '" finished']);
            }
        }

        return Command::SUCCESS;
    }

    private function addDatas($column, $datas, $faker, $keyC, $i, $output, $uniqueColumns): array
    {
        if ($column['generatedValue']) {
            $datas[$i][] = $i;
        } elseif (!empty($column['fields'])) {
            $parameters = [];
            $data       = '';

            foreach ($column['fields'] as $field) {
                if (!empty($field['parameters'])) {
                    foreach ($field['parameters'] as $parameter) {
                        if ($parameter instanceof DateTimeImmutable) {
                            $today     = new DateTime();
                            $interval  = $today->diff($parameter);
                            $parameter = $interval->format('%R%a') . ' days';
                        }
                        $parameters[] = $parameter;
                    }
                }

                if (!empty($field['name'])) {
                    if (str_contains($field['name'], 'Locales') && preg_match('~[\\\\/]Locales[\\\\/]([^\\\\/]+)~', $field['name'], $matches)) {
                        $faker = Factory::create($matches[1]);
                    }

                    $data = $this->getData($column, $faker, $keyC, $output, $uniqueColumns, $field, $parameters);

                    if (str_contains($field['name'], 'Locales') && preg_match('~[\\\\/]Locales[\\\\/]([^\\\\/]+)~', $field['name'], $matches)) {
                        $faker = Factory::create('fr_FR');
                    }
                }
            }

            $datas[$i][] = $data;
        } else {
            $datas[$i][] = $this->getData($column, $faker, $keyC, $output, $uniqueColumns);
        }

        return $datas;
    }

    private function getData(array $column, $faker, $keyC, $output, $uniqueColumns = [], $field = [], $parameters = [])
    {
        if ($column['nullable'] && mt_rand(0, 1)) {
            $data = null;
        } else if (!empty($field)) {
            if (empty($parameters)) {
                $data = $faker->{'' . $field['nameFaker']}();
            } else {
                $data = $faker->{'' . $field['nameFaker']}(...$parameters);
            }
        } else {
            $data = $this->fixtureHelper->getRandomFaker($column['type'], $column['length'], $column['precision']);
        }

        if (!empty($uniqueColumns[$keyC])) {
            $tentatives = 0;

            while ($data !== null && in_array($data, $uniqueColumns[$keyC]) && $tentatives < 10) {
                $data = $column['nullable'] && mt_rand(0, 1) ? null : $faker->{'' . $field['nameFaker']}(...$parameters);
                $tentatives++;
            }

            if ($tentatives >= 10) {
                $output->writeln(['Impossible de générer des fixtures car la colonne ' . $keyC . ' est unique et le système n\'arrive pas à attribuer une valeur différente sur chaque ligne']);
                die();
            }
        }

        if ($data !== null && !empty($uniqueColumns) && isset($uniqueColumns[$keyC])) {
            $uniqueColumns[$keyC][] = $data;
        }

        return $data;
    }
}
